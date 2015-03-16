<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   am_apa.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
DEFINE ('BOX_CONFIGURATION_777', 'Amazon Advanced Payment APIs Optionen');
DEFINE('MODULE_PAYMENT_AM_APA_TEXT_TITLE', 'Amazon Advanced Payment APIs');
DEFINE('MODULE_PAYMENT_AM_APA_STATUS_TITLE', 'Modul aktivieren?');
DEFINE('MODULE_PAYMENT_AM_APA_STATUS_DESC', 'Durch die entsprechende Auswahl k&ouml;nnen Sie das Login und Bezahlen mit Amazon aktivieren und deaktivieren.');
DEFINE('MODULE_PAYMENT_AM_APA_MERCHANTID_TITLE', 'Amazon H&auml;ndler ID');
DEFINE('MODULE_PAYMENT_AM_APA_MERCHANTID_DESC', 'Ihre Amazon H&auml;ndler ID (Merchant ID)');
DEFINE('MODULE_PAYMENT_AM_APA_MARKETPLACEID_TITLE', 'Amazon Marketplace ID');
DEFINE('MODULE_PAYMENT_AM_APA_MARKETPLACEID_DESC', 'Ihre Amazon Marketplace ID');
DEFINE('MODULE_PAYMENT_AM_APA_ACCESKEY_TITLE', 'MWS Zugangsschl&uuml;ssel-ID');
DEFINE('MODULE_PAYMENT_AM_APA_ACCESKEY_DESC', 'Dient zur Kommunikation und Authentifizierung mit Amazon Advanced Payment APIs');
DEFINE('MODULE_PAYMENT_AM_APA_SECRETKEY_TITLE', 'Geheimer Schl&uuml;ssel');
DEFINE('MODULE_PAYMENT_AM_APA_SECRETKEY_DESC', 'Dient zur Kommunikation und Authentifizierung mit Amazon Advanced Payment APIs');
define('MODULE_PAYMENT_AM_APA_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_AM_APA_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
DEFINE('MODULE_PAYMENT_AM_APA_MODE_TITLE', 'Betriebsmodus');
DEFINE('MODULE_PAYMENT_AM_APA_MODE_DESC', '');
DEFINE('MODULE_PAYMENT_AM_APA_PROVOCATION_TITLE', 'Fehler provozieren (nur Sandbox)');
DEFINE('MODULE_PAYMENT_AM_APA_PROVOCATION_DESC', 'Dient dazu, dass der Administrator das Ablehnen von Zahlungen, bzw. Kunden simulieren kann.');
DEFINE('MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK_TITLE', 'Bestellstatus f&uuml;r autorisierte Zahlungen');
DEFINE('MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK_DESC', 'Dieser Status wird gesetzt, nachdem Amazon die Zahlung autorisiert hat.');
DEFINE ('MODULE_PAYMENT_AM_APA_ALLOW_GUESTS_TITLE','Gastbestellungen erlauben?');
DEFINE ('MODULE_PAYMENT_AM_APA_ALLOW_GUESTS_DESC','Wenn aktiviert, k&ouml;nnen Kunden auch per Amazon Advanced Payment APIs bezahlen, wenn Sie kein Kundenkonto in Ihrem Shop angelegt haben.');
define('AMZ_YES', 'Ja');
define('AMZ_NO', 'Nein');
define('AMZ_SANDBOX', 'Testbetrieb');
define('AMZ_LIVE', 'Livebetrieb');
define('AMZ_AUTHORIZATION_CONFIG_TITLE', 'Wann/Wie soll die Zahlung autorisiert werden?');
define('AMZ_AUTHORIZATION_CONFIG_DESC', 'Die sofortige Autorisierung birgt ein geringf&uuml;gig h&ouml;heres Risiko, abgelehnt zu werden. Sie ist daher zu empfehlen, wenn Sie z.B. Downloadprodukte verkaufen und deshalb eine sofortige Zahlungsbest&auml;tigung ben&ouml;tigen, oder wenn Sie dem Kunden eine unmittelbare R&uuml;ckmeldung &uuml;ber den Erfolg der Autorisierung geben m&ouml;chten.<br/><br />Nach der Autorisierung haben Sie bis zu 30 Tage Zeit, die Ware zu versenden und damit die Zahlung auszul&ouml;sen. In den ersten sieben Tagen garantiert Amazon den Erfolg des Geldeinzugs.');
define('AMZ_CAPTURE_CONFIG_TITLE', 'Wann/Wie soll die Zahlung eingezogen werden?');
define('AMZ_CAPTURE_CONFIG_DESC', 'Bitte beachten Sie, dass ein Einziehen der Zahlung direkt nach der Autorisierung bei Amazon Payments nicht zul&auml;ssig ist. Sie sollten diese Option in jedem Fall nur nach R&uuml;cksprache mit Amazon aktivieren!');
define('AMZ_FAST_AUTH', 'w&auml;hrend des Checkouts/vor Abschluss der Bestellung');
define('AMZ_AUTH_AFTER_CHECKOUT', 'direkt nach der Bestellung');
define('AMZ_CAPTURE_AFTER_AUTH', 'direkt nach erfolgreicher automatischer Autorisierung (Hinweis beachten!)');
define('AMZ_AFTER_SHIPPING', 'nach Versand');
define('AMZ_MANUALLY', 'manuell');
define('AMZ_SHIPPED_STATUS_TITLE', 'Bestellstatus f&uuml;r versendete Bestellungen');
define('AMZ_SHIPPED_STATUS_DESC', 'Wichtig f&uuml;r den Einzug der Zahlung nach Versand');
define('AMZ_REVOCATION_ID_TITLE', 'Content-ID f&uuml;r Widerrufbelehrung');
define('AMZ_AGB_ID_TITLE', 'Content-ID f&uuml; AGB');
define('AMZ_BUTTON_COLOR_ORANGE', 'Amazon-Gelb');
define('AMZ_BUTTON_COLOR_TAN', 'Grau');
define('AMZ_BUTTON_SIZE_MEDIUM', 'normal');
define('AMZ_BUTTON_SIZE_LARGE', 'gro&szlig;');
define('AMZ_BUTTON_SIZE_XLARGE', 'sehr gro&szlig;');
define('AMZ_TX_TYPE_HEADING', 'Transaktionstyp');
define('AMZ_TX_TIME_HEADING', 'Zeit');
define('AMZ_TX_STATUS_HEADING', 'Status');
define('AMZ_TX_LAST_CHANGE_HEADING', 'Letzte &Auml;nderung');
define('AMZ_TX_ID_HEADING', 'Amazon-Transaktions-ID');
define('AMZ_AUTH_TEXT', 'Autorisierung');
define('AMZ_ORDER_TEXT', 'Bestellung');
define('AMZ_CAPTURE_TEXT', 'Einzug');
define('AMZ_REFUND_TEXT', 'R&uuml;ckzahlung');
define('AMZ_TX_AMOUNT_HEADING', 'Betrag');
define('AMZ_IPN_URL_TITLE', 'URL f&uuml;r Amazon IPN');
define('AMZ_TX_EXPIRATION_HEADING', 'G&uuml;ltig bis');
define('AMZ_HISTORY', 'Amazon Payments Verlauf');
define('AMZ_ORDER_AUTH_TOTAL', 'Autorisierter Betrag');
define('AMZ_ORDER_CAPTURE_TOTAL', 'Eingezogener Betrag');
define('AMZ_SUMMARY', 'Amazon Zusammenfassung');
define('AMZ_ACTIONS', 'Amazon Aktionen');
define('AMZ_CAPTURE_FROM_AUTH_HEADING', 'Autorisierte Zahlungen einziehen');
define('AMZ_TX_ACTION_HEADING', 'Aktionen');
define('AMZ_CAPTURE_TOTAL_FROM_AUTH', 'Vollst&auml;ndigen Betrag einziehen');
define('AMZ_AUTHORIZE', 'Zahlung autorisieren');
define('AMZ_AUTHORIZATION_SUCCESSFULLY_REQUESTED', 'Autorisierungsanfrage wurde erfolgreich gestellt');
define('AMZ_AUTHORIZATION_REQUEST_FAILED', 'Autorisierungsanfrage fehlgeschlagen');
define('AMZ_CAPTURE_SUCCESS', 'Einzug erfolgreich');
define('AMZ_CAPTURE_FAILED', 'Einzug fehlgeschlagen');
define('AMZ_AMOUNT_LEFT_TO_OVER_AUTHORIZE', 'Zus&auml;tzliche Autorisierung');
define('AMZ_AMOUNT_LEFT_TO_AUTHORIZE', 'Standard-Autorisierung');
define('AMZ_TYPE', 'Typ');
define('AMZ_REFUNDS', 'R&uuml;ckzahlungen');
define('AMZ_REFUND', 'Zur&uuml;ckzahlen');
define('AMZ_REFUND_SUCCESS', 'R&uuml;ckzahlung erfolgreich eingereicht');
define('AMZ_REFUND_FAILED', 'R&uuml;ckzahlung fehlgeschlagen');
define('AMZ_REFRESH', 'Aktualisieren');
define('AMZ_CAPTURE_AMOUNT_FROM_AUTH', 'Teilbetrag einziehen');
define('AMZ_REFUND_TOTAL', 'Vollst&auml;ndig erstatten');
define('AMZ_REFUND_AMOUNT', 'Teilweise erstatten');
define('AMZ_TX_AMOUNT_REFUNDED_HEADING', 'Erstattet');
define('AMZ_TX_SUM', 'Summe');
define('AMZ_TX_AMOUNT_REFUNDABLE_HEADING', 'Noch m&ouml;glich');
define('AMZ_TX_AMOUNT_POSSIBLE_HEADING', 'Maximal m&ouml;glich');
define('AMZ_AUTHORIZE_AMOUNT', 'Teilbetrag autorisieren');
define('AMZ_TX_AMOUNT_NOT_AUTHORIZED_YET_HEADING', 'Noch nicht autorisierter Betrag');
define('AMZ_REFUND_OVER_AMOUNT', 'Noch mehr zur&uuml;ckzahlen');
define('AMZ_FINISHED_REFRESHING_ORDER', 'Bestellung aktualisiert');
define('AMZ_OVER_AUTHORIZE_AMOUNT', 'Noch mehr autorisieren');
define('MODULE_PAYMENT_AM_APA_IPN_STATUS_TITLE', 'Status Updates per IPN empfangen');
define('AMZ_CRON_URL_TITLE', 'URL f&uuml;r Cronjob');
define('MODULE_PAYMENT_AM_APA_CRON_STATUS_TITLE', 'Cronjob f&uuml;r Status Updates freischalten');
define('AMZ_AGB_DISPLAY_MODE_TITLE', 'Anzeige von AGB und Widerruf');
define('AMZ_AGB_DISPLAY_MODE_SHORT', 'Nur Hinweis');
define('AMZ_AGB_DISPLAY_MODE_LONG', 'Vollst&auml;ndige Darstellung mit Checkbox');
define('AMZ_CRON_PW_TITLE', 'Passwort f&uuml;r Cronjob');
define('AMZ_SOFT_DECLINE_SUBJECT_TITLE', 'E-Mail-Betreff abgelehnte Zahlung');
define('AMZ_PAYMENT_NAME_TITLE', 'E-Mail-Absender Name');
define('AMZ_PAYMENT_EMAIL_TITLE', 'E-Mail-Absender Adresse');
define('AMZ_HARD_DECLINE_SUBJECT_TITLE', 'E-Mail-Betreff abgelehnte Bestellung');
define('AMZ_SEND_MAILS_ON_DECLINE_TITLE', 'Kunden automatisch benachrichtigen, wenn Zahlungen abgelehnt wurden');
define('AMZ_FASTAUTH_SOFT_DECLINED', 'Bitte w&auml;hlen Sie eine andere Zahlungsart');
define('AMZ_FASTAUTH_HARD_DECLINED', 'Ihre Zahlung wurde von Amazon abgelehnt - bitte verwenden Sie daher eine andere Zahlungsart');
define('AMZ_UNKNOWN_ERROR', 'Ihre Bestellung konnte leider nicht ausgef&uuml;hrt werden - bitte versuchen Sie es erneut mit einer anderen Zahlungsart');
define('AMZ_CANCEL_ORDER', 'Amazon Zahlungsvorgang abbrechen');
define('AMZ_CLOSE_ORDER', 'Bestellung abschlie&szlig;en');
define('AMZ_ORDER_CANCELLED', 'Zahlungsvorgang abgebrochen');
define('AMZ_ORDER_CLOSED', 'Bestellung abgeschlossen');
define('AMZ_SHOW_ON_CHECKOUT_PAYMENT_TITLE', 'Amazon-Button in normalem Checkout anzeigen');
define('AMZ_DEBUG_MODE_TITLE', 'Debug Modus');
define('AMZ_DEBUG_MODE_DESC', 'Im Debug Modus ist die Zahlung per Amazon Advanced Payment APIs nur f&uuml;r Admins sichtbar');
define('AMZ_EXCLUDED_SHIPPING_TITLE', 'Versandarten ausschlie&szlig;en');
define('AMZ_NEW_VERSION_AVAILABLE', 'Es ist eine neue Version dieses Moduls verf&uuml;gbar');
define('AMZ_VERSION_IS_GOOD', 'Ihre Version des Moduls ist aktuell');
define('AMZ_EXCLUDE_PRODUCTS', 'Produkte ausschlie&szlig;en');
define('AMZ_SEARCH', 'Suche');
define('AMZ_EXCLUDED_PRODUCTS', 'Ausgeschlossene Produkte');
define('AMZ_SEARCH_RESULT', 'Suchergebnis');
define('AMZ_EXCLUDED_PRODUCTS_TITLE', 'Produkte von Zahlung mit Amazon ausschlie&szlig;en');
define('AMZ_EXCLUDED_PRODUCTS_OPEN', '&Ouml;ffnen');
define('AMZ_ALL_MANUFACTURERS', 'Alle Hersteller');
define('AMZ_INCLUDE_ALL_PRODUCTS', 'Alle entfernen');
define('AMZ_EXCLUDE_ALL_PRODUCTS', 'Alle ausschlie&szlig;en');
define('AMZ_TEMPLATE_1', 'Template 1');
define('AMZ_TEMPLATE_2', 'Template 2');
define('AMZ_TEMPLATE_TITLE', 'Template-Auswahl');
define('AMZ_CANCEL_ORDER_FROM_WALLET', 'Zahlung mit Amazon Payments abbrechen');
define('AMZ_WALLET_INTRO', 'Die von Ihnen gew&auml;hlte Zahlungsart ist leider momentan nicht verf&uuml;gbar. Bitte w&auml;hlen Sie eine andere.');
define('AMZ_DOWNLOAD_ONLY_TITLE', 'In diesem Shop werden nur virtuelle Artikel verkauft');
define('AMZ_DOWNLOAD_ONLY_DESC', 'Wenn ja, wird bei Auswahl der Autorisierung w&auml;hrend des Checkoutvorgangs keine Versandadresse abgefragt');
define('AMZ_HEADING_AMAZON_PAYMENTS_ACCOUNT', 'Amazon Payments Konto');
define('AMZ_HEADING_GENERAL_SETTINGS', 'Allgemeine Einstellungen');
define('AMZ_HEADING_DESIGN_SETTINGS', 'Design Einstellungen');
define('AMZ_HEADING_IPN_SETTINGS', 'IPN Einstellungen');
define('AMZ_HEADING_CRONJOB_SETTINGS', 'Cronjob Einstellungen');
define('AMZ_HEADING_MAIL_SETTINGS', 'E-Mail Optionen');

# UPDATE V2.01
define('AMZ_DB_UPDATE_WARNING', 'Sie haben ein Modul-Update durchgef&uuml;hrt und m&uuml;ssen nun die Datenbank aktualisieren');
define('AMZ_DB_UPDATE_BUTTON_TEXT', 'Datenbank aktualisieren');
define('AMZ_IPN_PW_TITLE', 'Passwort f�r IPN');
define('AMZ_SET_SELLER_ORDER_ID_TITLE', 'Shop-Bestellnummer an Amazon &uuml;bertragen');
define('AMZ_SET_SELLER_ORDER_ID_DESC', 'Achtung: Aufgrund von Bestellabbr&uuml;chen kann es zu L&uuml;cken bei den Bestellnumern kommen');
define('AMZ_ORDER_REF_HEADING', 'Amazon Bestellreferenz');
define('AMZ_ORDERS_ID_HEADING', 'Shop Bestellung');
define('AMZ_TRANSACTION_HISTORY', 'Amazon Transaktionsprotokoll');
define('AMZ_BACK', 'Zur&uuml;ck');
define('AMZ_MERCHANT_ID_INVALID', 'Keine Aktionen m&ouml;glich - Ihre Amazon H&auml;ndler-ID stimmt leider nicht mehr der Transaktion &uuml;berein');
define('AMZ_STATUS_NONAUTHORIZED_TITLE', 'Bestellstatus f&uuml;r noch nicht autorisierte Zahlungen');

# UPDATE V2.10
define('MODULE_PAYMENT_AM_APA_LPA_MODE_TITLE', 'Betriebsmodus Login/Pay');
define('MODULE_PAYMENT_AM_APA_LPA_MODE_DESC', '');
define('MODULE_PAYMENT_AM_APA_CLIENTID_TITLE', 'Client-ID f&uuml;r Login &amp; Pay');
define('MODULE_PAYMENT_AM_APA_CLIENTID_DESC', 'Dient zur Kommunikation und Authentifizierung mit Login &amp; Pay by Amazon');
define('AMZ_SET_ADDRESS_TITLE', 'Vielen Dank f&uuml;r Ihre Anmeldung mit Amazon Payments');
define('AMZ_SET_ADDRESS_INTRO', 'Um fortfahren zu k&ouml;nnen, ben&ouml;tigen wir von Ihnen die Angabe einer Standard-Adresse. Sie k&ouml;nnen w&auml;hrend Ihres Einkaufs jederzeit eine andere Adresse w&auml;hlen.');
define('AMZ_CONNECT_ACCOUNTS_TITLE', 'Vielen Dank f&uuml;r Ihre Anmeldung mit Amazon Payments');
define('AMZ_CONNECT_ACCOUNTS_INTRO', 'In unserem Shop existiert bereits ein Benutzerkonto mit dieser E-Mail-Addresse. Bitte geben Sie Ihr Passwort ein, um dieses mit Ihrem Amazon-Konto zu verkn&uuml;pfen');
define('AMZ_PASSWORD', 'Ihr Passwort');
define('AMZ_CONNECT_ACCOUNTS', 'Konten jetzt verkn&uuml;pfen');
define('AMZ_CONNECT_ACCOUNTS_ERROR', 'Fehler: Falsches Passwort');
define('MODULE_PAYMENT_AM_APA_POPUP_TITLE', 'Wenn m&ouml;glich Amazon-Login in Popup');
define('MODULE_PAYMENT_AM_APA_POPUP_DESC', 'Soll der Amazon-Login vorzugsweise in einem Popup dargestellt werden? Andernfalls, wird der Kunde zu Amazon und im Anschluss wieder in Ihren Shop weitergeleitet. Der Login im Popup ist nur m&ouml;glich, wenn Ihr gesamter Shop SSL-verschl&uuml;sselt ist.');
define('AMZ_LOGIN_PROCESSING_TITLE', 'Vielen Dank f&uuml;r Ihre Anmeldung mit Amazon Payments');
define('AMZ_LOGIN_PROCESSING_INTRO', 'Sie werden in wenigen Sekunden weitergeleitet...');
define('AMZ_BUTTON_SIZE_TITLE', 'Gr&ouml;&szlig;e des Amazon-Checkout-Buttons (pay-Modus)');
define('AMZ_BUTTON_SIZE_TITLE_LPA', 'Gr&ouml;&szlig;e des Amazon-Checkout-Buttons (Login/Login&amp;Pay-Modus)');
define('AMZ_BUTTON_COLOR_TITLE', 'Farbe des Amazon-Checkout-Buttons (pay-Modus)');
define('AMZ_BUTTON_COLOR_TITLE_LPA', 'Farbe des Amazon-Checkout-Buttons (Login/Login&amp;Pay-Modus)');
define('AMZ_BUTTON_COLOR_TAN_LIGHT', 'Hell-Grau');
define('AMZ_BUTTON_COLOR_TAN_DARK', 'Dunkel-Grau');
define('AMZ_BUTTON_SIZE_SMALL', 'klein');
define('AMZ_BUTTON_TYPE_LOGIN_TITLE', 'Button Typ Login');
define('AMZ_BUTTON_TYPE_PAY_TITLE', 'Button Typ Bezahlen');
define('AMZ_BUTTON_TYPE_LOGIN_LWA', 'Login &uuml;ber Amazon');
define('AMZ_BUTTON_TYPE_LOGIN_LOGIN', 'Login');
define('AMZ_BUTTON_TYPE_LOGIN_A', 'Nur ein "A"');
define('AMZ_BUTTON_TYPE_PAY_PWA', 'Bezahlen &uuml;ber Amazon');
define('AMZ_BUTTON_TYPE_PAY_PAY', 'Bezahlen');
define('AMZ_BUTTON_TYPE_PAY_A', 'Nur ein "A"');
define('AMZ_SAVE', 'Speichern');
define('AMZ_CONFIGURATION', 'Konfiguration');
define('AMZ_PROTOCOL', 'Transaktionsprotokoll');
define('AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE_TITLE', 'Bestellungen nach erfolgreicher Zahlung abschlie&szlig;en');
define('AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE_DESC', 'Sollen Bestellungen, bei denen der Gesamtbetrag erfolgrech eingezogen wurde, bei Amazon als abgeschlossen markiert werden? Es sind dann f&uuml;r diese Bestellunng nur noch R&uuml;ckzahlungen m&ouml;glich.');
define('AMZ_BUTTON_TYPE_PAY_DESC', 'Nur bei Betriebsmodus "Login & Pay"');
define('AMZ_INVALID_SECRET', 'Der von Ihnen eingegebene geheime Schl�ssel ist nicht korrekt');
define('AMZ_INVALID_MERCHANT_ID', 'Die von Ihnen eingegebene Amazon H�ndler ID ist nicht korrekt');
define('AMZ_INVALID_ACCESS_KEY', 'Die von Ihnen eingegebene MWS Zugangsschl�ssel-ID ist nicht korrekt');
define('AMZ_CREDENTIALS_SUCCESS', 'Die von Ihnen eingegebenen Zugangsdaten wurden erfolgreich validiert');
?>
