(function($) {
// alert($.fn.jquery)
var xtc = window.xtc = window.xtc || {};
xtc.notifications = xtc.notifications || $(document);

/**
 * Override standard xtc functions
 */
window.rowOverEffect = window.rowOutEffect = window.submitFunction = $.noop;

xtc.AjaxCheckout = {
  BOX_MAPPING: {
    "login":              "#ajax-checkout-login",
    "shipping_modules":   "#ajax-checkout-shipping-modules",
    "payment_modules":    "#ajax-checkout-payment-modules",
    "shipping_address":   "#ajax-checkout-shipping-address",
    "payment_address":    "#ajax-checkout-payment-address",
    "comments":           "#ajax-checkout-comments",
    "agb":                "#ajax-checkout-tos",
    "revocation":         "#ajax-checkout-revocation",
    "products":           "#ajax-checkout-shopping-cart"
  },
  
  initialize: function() {
    this.container  = $("#ajax-checkout").addClass("js");
    
    this._initBoxes();
    this._observe();
  },
  
  _observe: function() {
    $(function() { $("a.help-link").nyroModal(); });
    
    this.container.find(".header").live("click", function() {
      $(this).toggleClass("closed").next().slideToggle("fast");
    }).queue();
    
    xtc.notifications.bind("ajax_checkout.scroll_into_view", $.proxy(function() {
      var offset = this.container.offset();
      window.scrollTo(offset.left, offset.top);
    }, this));
    
    xtc.notifications.bind("ajax_checkout.submit", $.proxy(function(event, options) {
      this.ajaxSubmit(options);
    }, this));
    
    xtc.notifications.bind("ajax_checkout.success", $.proxy(function(event, options) {
      if (options.alert) {
        alert(options.alert);
      }
      if (options.redirect) {
        location.href = options.redirect;
      }
    }, this));
  },
  
  _initBoxes: function() {
    $.each(this.BOX_MAPPING, $.proxy(function(key, value) {
      var obj = this.boxes[key],
          container = $(value);
      obj && obj.initialize(container);
      
      // Collapse box
      if (!this.BOX_CONFIGS[key]) {
        container
          .find(".content").hide().end()
          .find(".header").addClass("closed");
      }
    }, this));
    
    this.boxes.order_total.initialize();
  },
  
  ajaxSubmit: function(options) {
    var container   = $(options.form || options.container),
        boxHeader   = container.parents(".box-full, .box-half"),
        indicator   = boxHeader.find(".ajax-indicator"),
        formFields  = container.find("input, select, textarea"),
        ajaxOptions = {
          url: location.href,
          contentType: "application/x-www-form-urlencoded; charset=utf-8",
          data: options.data,
          beforeSend: function() {
            indicator.show();
            formFields.attr("disabled", "disabled");
          },
          success: function(data, textStatus, xhr) {
            var jsonHeader = xhr.getResponseHeader("X-Ajax-Checkout");
            if (jsonHeader) {
              // complete handler will take care
              return;
            }
            
            xtc.notifications.trigger("ajax_checkout.success", [data]);
            (options.callback || $.noop)(data);
          },
          complete: function(xhr) {
            var jsonHeader = xhr.getResponseHeader("X-Ajax-Checkout");
            if (jsonHeader) {
              var data = $.parseJSON(jsonHeader);
              xtc.notifications.trigger("ajax_checkout.success", [data]);
              (options.callback || $.noop)(data);
            }
            indicator.hide();
            formFields.removeAttr("disabled");
          }
        };
    
    if (options.form) {
      container.ajaxSubmit(ajaxOptions);
    } else {
      $.ajax(ajaxOptions);
    }
  }
};


//------------------------- BOXES -------------------------\\
xtc.AjaxCheckout.boxes = {};
xtc.AjaxCheckout.boxes.login = {
  initialize: function(container) {
    this.container = container;
    this.loginForm = this.container.find(".login");
    this.registerForm = this.container.find(".register");
    this.loginLink = this.container.find(".login-link");
    this.registerLink = this.container.find(".register-link");
    this.errorMessage = this.container.find(".error-message");
    
    this._observe();
  },
  
  _observe: function() {
    this.loginLink.add(this.registerLink).click($.proxy(function(event) {
      this.loginForm.add(this.registerForm).toggle();
      this.errorMessage.removeClass("error-message-visible").html("");
      xtc.notifications.trigger("ajax_checkout.scroll_into_view");
      event.preventDefault();
    }, this));
    
    this.loginForm.add(this.registerForm).delegate("form", "submit", $.proxy(function(event) {
      this.errorMessage.removeClass("error-message-visible").html("");
      xtc.notifications.trigger("ajax_checkout.submit", {
        form: event.currentTarget,
        callback: $.proxy(function(data) {
          if (data.error) {
            this.errorMessage.addClass("error-message-visible").html(data.error);
          } else {
            this._proceedAfterLogin();
          }
        }, this)
      });
      event.preventDefault();
    }, this));
    
    this.registerForm.delegate("select[name=country]", "change", $.proxy(function(event) {
      $.ajax({
        url: location.href,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: { ajax_action: "get_states", country: event.currentTarget.value },
        success: $.proxy(function(data) {
          var listItem = this.registerForm.find("li:has(.ajax-checkout-state-container)");
          if (data.state_select) {
            listItem.show();
            listItem.find(".ajax-checkout-state-container").html(data.state_select);
          } else {
            listItem.hide();
          }
        }, this)
      });
    }, this));
    
    // Don't show the password fields for guest accounts
    this.registerForm.delegate("input:radio[name=account_type]", "change", function() {
      if ($("#ajax-checkout-account-type-default-guest")[0].checked) {
        $("#ajax-checkout-password-container").hide();
      } else {
        $("#ajax-checkout-password-container").show();
      }
    });
  },
  
  _proceedAfterLogin: function() {
    var indicators = $("#ajax-checkout .ajax-indicator").show();
    
    xtc.notifications.trigger("ajax_checkout.submit", {
      data: { ajax_action: "after_login" },
      callback: function() {
        $("#ajax-checkout-logged-out-content, #ajax-checkout-ext-login-box").slideUp(function() {
          $(this).remove();
          $("#ajax-checkout-ext-create-account-link, #ajax-checkout-ext-login-link").hide();
          $("#ajax-checkout-logged-in-content, #ajax-checkout-total-form, #ajax-checkout-ext-logout-link").fadeIn();
          setTimeout(function() { indicators.hide(); }, 1000);
          xtc.notifications.trigger("ajax_checkout.logged_in");
        });
      }
    });
  }
};




xtc.AjaxCheckout.boxes.shipping_modules = {
  initialize: function(container) {
    this.container = container;
    this.form = this.container.find("form:first");
    this.block = this.form.find(".shipping-options");
    this.errorMessage = container.find(".error-message");
    
    this._observe();
  },
  
  _observe: function() {
    xtc.notifications.bind("ajax_checkout.success", $.proxy(function(event, data) {
      if (data.shipping_options) {
        this.block.html(data.shipping_options);
      }
      
      if (typeof(data.is_free_shipping) != "undefined") {
        if (data.is_free_shipping) {
          this.form.hide();
          this.container.find(".free-shipping-text").show();
        } else {
          this.form.show();
          this.container.find(".free-shipping-text").hide();
        }
      }
      
      if (typeof(data.is_virtual) != "undefined") {
        if (data.is_virtual) {
          this.form.hide();
          this.container.find(".virtual-text").show();
        } else {
          this.form.show();
          this.container.find(".virtual-text").hide();
        }
      }
    }, this));
    
    this.form.submit($.proxy(function(event) {
      this.errorMessage.removeClass("error-message-visible").html("");
      xtc.notifications.trigger("ajax_checkout.submit", {
        form: event.currentTarget,
        callback: $.proxy(function(data) {
          var optionRows = this.block.find(".option-row").removeClass("option-row-selected"),
              selectedRow = optionRows.filter(":has(input:radio[checked])");
          
          if (data.error) {
            selectedRow.addClass("option-row-focussed");
            this.errorMessage.addClass("error-message-visible").html(data.error);
          } else {
            optionRows.removeClass("option-row-selected");
            selectedRow.addClass("option-row-selected");
          }
        }, this)
      });
      event.preventDefault();
    }, this));
    
    this.block.delegate("input:radio", "change", $.proxy(function() {
      this.form.submit();
    }, this));
  }
};




xtc.AjaxCheckout.boxes.payment_modules = {
  initialize: function(container) {
    this.container = container;
    this.form = container.find("form:first");
    this.block = this.form.find(".payment-options");
    this.errorMessage = container.find(".error-message:last");
    
    this._observe();
    
    if (xtc.AjaxCheckout.PAYMENT_FORM_DATA) {
      this._restoreState(xtc.AjaxCheckout.PAYMENT_FORM_DATA);
    }
  },
  
  _storeState: function() {
    this.state = {};
    var elements = this.form[0].elements;
    for (var i=0; i<elements.length; i++) {
      var element = elements[i];
      this.state[element.name] = $(element).val();
    }
  },
  
  _restoreState: function(state) {
    this.state = this.state || state || {};
    var elements = this.form[0].elements;
    for (var i=0; i<elements.length; i++) {
      var element = elements[i];
      if (element.type == "radio" || element.type == "checkbox") {
        continue;
      }
      $(element).val(this.state[element.name]);
    }
  },
  
  _observe: function() {
    xtc.notifications.bind("ajax_checkout.success", $.proxy(function(event, data) {
      if (data.payment_options) {
        this._storeState();
        this.block.html(data.payment_options);
        this._restoreState();
      }
      if (data._payment_error) {
        this.errorMessage.addClass("error-message-visible").html(data._payment_error);
      }
      if (typeof(data.gvcover) != "undefined") {
        if (data.gvcover) {
          this.form.hide();
          this.container.find(".gvcover-text").show();
        } else {
          this.form.show();
          this.container.find(".gvcover-text").hide();
        }
      }
      if (typeof(data.gvcover_html) != "undefined") {
        if (data.gvcover_html) {
          this.container.find(".gift-module").show().find("table:first").html(data.gvcover_html);
        } else {
          this.container.find(".gift-module").hide();
        }
      }
    }, this));
    
    this.form.submit($.proxy(function(event) {
      this.block.find(".error-message").html("");
      xtc.notifications.trigger("ajax_checkout.submit", {
        form: event.currentTarget,
        callback: $.proxy(function(data) {
          var optionRows = this.block.find(".option-row").removeClass("option-row-selected"),
              selectedRow = optionRows.length > 1 ? optionRows.filter(":has(input:radio[checked])") : optionRows.filter(":first");
          
          if (data.error) {
            selectedRow
              .addClass("option-row-focussed")
              .find(".error-message").html(data.error);
          } else {
            optionRows.removeClass("option-row-selected");
            selectedRow.addClass("option-row-selected");
          }
        }, this)
      });
      event.preventDefault();
    }, this));
    
    this.block.delegate("input:radio", "change", $.proxy(function(event) {
      this.errorMessage.removeClass("error-message-visible").html("");
      var radio = $(event.currentTarget),
          payment = radio.val(),
          listItem = radio.parents("li"),
          paymentBody = listItem.find(".payment-option-body");
          hasDetails = !!paymentBody.find(".payment-fieldset, .payment-description").length;
      
      this.block.find(".option-row").removeClass("option-row-focussed");
      listItem.addClass("option-row-focussed");
      
      this.block.find(".payment-option-body").hide();
      
      if (hasDetails) {
        paymentBody.slideDown();
      } else {
        this.form.submit();
      }
    }, this));
    
    this.container.delegate("form.gift-module input:checkbox[name=cot_gv]", "change", function(event) {
      $(event.currentTarget).val("1").parents("form").submit();
    });
    
    this.container.delegate("form.gift-module", "submit", $.proxy(function(event) {
      xtc.notifications.trigger("ajax_checkout.submit", { form: event.currentTarget });
      event.preventDefault();
    }, this));
  }
};




xtc.AjaxCheckout.boxes.address = {
  initialize: function(container) {
    this.container = container;
    this.errorMessage = this.container.find(".error-message");
    this.block = container.find("address.selected");
    
    this._observe();
  },
  
  _observe: function() {
    this.container.delegate("select.ajax-checkout-address-dropdown", "change", $.proxy(function(event) {
      var dropdown = $(event.currentTarget);
      xtc.notifications.trigger("ajax_checkout.submit", {
        type: "post",
        container: dropdown.parent(),
        data: {
          address_id: dropdown.val(),
          ajax_action: "update_address_by_dropdown",
          type: this.type
        },
        callback: $.proxy(function(data) {
          this.container.find("input.back").trigger("click");
        }, this)
      });
    }, this));
    
    this.container.find("form").submit($.proxy(function(event) {
      this.errorMessage.removeClass("error-message-visible").html("");
      var form = $(event.currentTarget);
      xtc.notifications.trigger("ajax_checkout.submit", {
        form: form,
        callback: $.proxy(function(data) {
          if (data.error) {
            this.errorMessage.addClass("error-message-visible").html(data.error);
            return;
          }
          
          this.container.find("input.back").trigger("click");
          form.get(0).reset();
        }, this)
      });
      event.preventDefault();
    }, this));
    
    this.container.delegate("input.edit, input.back", "click", $.proxy(function(event) {
      this.container.find(".change-address, .show-address").toggle();
      event.preventDefault();
    }, this));
    
    this.container.delegate("input.edit", "click", $.proxy(function(event) {
      xtc.notifications.trigger("ajax_checkout.submit", {
        type: "get",
        container: $(event.currentTarget),
        data: { ajax_action: "get_address_dropdown", type: this.type }
      });
    }, this));
    
    this.container.delegate("select[name=country]", "change", $.proxy(function(event) {
      $.ajax({
        url: location.href,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: { ajax_action: "get_states", country: event.currentTarget.value },
        success: $.proxy(function(data) {
          var listItem = this.container.find("li:has(.ajax-checkout-state-container)");
          if (data.state_select) {
            listItem.show();
            listItem.find(".ajax-checkout-state-container").html(data.state_select);
          } else {
            listItem.hide();
          }
        }, this)
      });
    }, this));
    
    xtc.notifications.bind("ajax_checkout.success", $.proxy(function(event, data) {
      var address = data[this.type + "_address"];
      if (address) {
        this.block.html(address);
      }
      
      var dropdown = data[this.type + "_address_dropdown"];
      if (dropdown) {
        this.container.find("select.ajax-checkout-address-dropdown").replaceWith(dropdown);
      }
    }, this));
  }
};

xtc.AjaxCheckout.boxes.shipping_address = $.extend({ type: "shipping" }, xtc.AjaxCheckout.boxes.address);
xtc.AjaxCheckout.boxes.payment_address = $.extend({ type: "payment" }, xtc.AjaxCheckout.boxes.address);




xtc.AjaxCheckout.boxes.products = {
  initialize: function(container) {
    this.container = container;
    this.block = container.find(".content");
    this.count = container.find(".products-amount");
    
    this._observe();
  },
  
  _observe: function() {
    this.container.delegate("a.products-decrease, a.products-increase", "click", function(event) {
      var link = $(this),
          listItem = link.parents("li");
      if (+listItem.find(".products-qty").text() <= 1 && link.hasClass("products-decrease")) {
        listItem.find("a.products-remove").click();
        return;
      }
      xtc.notifications.trigger("ajax_checkout.submit", {
        container: this,
        data: {
          type:         this.hash.substr(1),
          products_id:  this.rel,
          ajax_action:  "update_product"
        },
        callback: function(data) {
          listItem.find(".products-qty").html(data.products_qty);
          listItem.find(".products-price").html(data.products_price);
        }
      });
      event.preventDefault();
    });
    
    this.container.delegate("a.products-remove", "click", function(event) {
      var link = $(this);
      xtc.notifications.trigger("ajax_checkout.submit", {
        data: {
          type:         this.hash.substr(0, 1),
          products_id:  this.rel,
          ajax_action:  "remove_product"
        },
        callback: function(data) {
          if (data.success) {
            link.parents("li").fadeOut(function() { $(this).remove(); });
          }
        },
        container: this
      });
      event.preventDefault();
    });
    
    this.container.delegate("select", "change", function(event) {
      var select = $(this),
          listItem = select.parents("li[id^=ajax-checkout-product]");
      xtc.notifications.trigger("ajax_checkout.submit", {
        data: {
          attribute:    this.value,
          ajax_action:  "update_attribute"
        },
        callback: function(data) {
          if (data.restore_attribute) {
            select.val(data.restore_attribute);
          }
          if (data.products_price) {
            listItem.find(".products-price").html(data.products_price);
          }
        },
        container: select.parent()
      });
    });
    
    xtc.notifications.bind("ajax_checkout.success", $.proxy(function(event, data) {
      if (data.products) {
        this.block.html(data.products);
      }
      
      if (data.product_prices) {
        $.each(data.product_prices, function(id, price) {
          $("ajax-checkout-product-" + id).html(price);
        });
      }
      
      if (typeof(data.products_count) != "undefined") {
        this.count.html(data.products_count);
      }
    }, this));
  }
};




xtc.AjaxCheckout.boxes.order_total = {
  initialize: function(container) {
    this.form = $("#ajax-checkout-total-form");
    this.block = this.form.find("#ajax-checkout-order-total");
    this.hiddenInputs = this.form.find("#ajax-checkout-payment-data");
    this.button = this.form.find("#ajax-checkout-button");
    this.errorMessage = this.form.find(".error-message");
    
    this._observe();
  },
  
  _observe: function() {
    this.form.bind("submit", $.proxy(function(event) {
      event.preventDefault();
      this.errorMessage.removeClass("error-message-visible").html("");
      
      var agbCheckbox = $("#agb-checkbox");
      if (agbCheckbox.length && !agbCheckbox[0].checked) {
        this.errorMessage.addClass("error-message-visible").html(xtc.AjaxCheckout.AGB_ERROR);
        return;
      }
      
      var revocationCheckbox = $("#revocation-checkbox");
      if (revocationCheckbox.length && !revocationCheckbox[0].checked) {
        this.errorMessage.addClass("error-message-visible").html(xtc.AjaxCheckout.REVOCATION_ERROR);
        return;
      }
      
      xtc.notifications.trigger("ajax_checkout.submit", {
        form: event.currentTarget,
        data: { ajax_action: "checkout_check", comments: $("#ajax-checkout-comments-text").val() },
        callback: $.proxy(function(data) {
          if (data.checkout_error) {
            this.errorMessage.addClass("error-message-visible").html(data.checkout_error);
          }
          if (data.success) {
            var buttons = this.form.find("[type=image], [type=submit]");
            buttons.attr("disabled", "disabled");
            setTimeout(function() { buttons.removeAttr("disabled"); }, 4000);
            this.form[0].submit();
          }
        }, this)
      });
    }, this));
    
    xtc.notifications.bind("ajax_checkout.logged_in", $.proxy(function(event) {
      this.button.show();
    }, this));
    
    xtc.notifications.bind("ajax_checkout.success", $.proxy(function(event, data) {
      if (data.order_total) {
        this.block.html(data.order_total);
      }
      if (typeof(data.hidden_inputs) != "undefined") {
        this.hiddenInputs.html(data.hidden_inputs || "");
      }
      if (data.form_url) {
        this.form.attr("action", data.form_url);
      }
    }, this));
  }
};





/**
 * Session Timeout
 * Displays a modal window after the session became invalid
 */
xtc.SessionTimeout = {
  initialize: function() {
    this.start();
    xtc.notifications.bind("ajax_checkout.success", $.proxy(this.reset, this));
  },
  
  start: function() {
    this._expireTime = this.LIFETIME * 1000;
    this._setTimer();
  },

  _setTimer: function() {
    if (this._expireTime == 0 || isNaN(this._expireTime)) {
      return;
    }
    
    this._timer = setTimeout($.proxy(this._expired, this), this._expireTime);
  },

  _expired: function() {
    $.nyroModalManual({ url: "lang/" + this.LANGUAGE + "/ajax_checkout_session_expired.php" });
  },

  reset: function() {
    clearTimeout(this._timer);
    this._setTimer();
  }
};
})(jQuery);

// Remove the following line if you experience issues with several jQuery plugins
$.noConflict(true);