UPDATE `c2_sc_21`.`cm_file_flags` SET `file_flag_name` = 'Mehr über...' WHERE `cm_file_flags`.`file_flag` = 1;
UPDATE `c2_sc_21`.`cm_file_flags` SET `file_flag_name` = 'Informationen' WHERE `cm_file_flags`.`file_flag` = 2;

UPDATE `c2_sc_21`.`content_manager` SET `content_text` = 'Fügen Sie hier Ihr Impressum ein.' WHERE `content_manager`.`content_id` = 9;
UPDATE `c2_sc_21`.`content_manager` SET `content_text` = '<table cellSpacing=0 cellPadding=0>
<tbody>
<tr>
<td class=main><STRONG>Gutscheine kaufen </STRONG></td></tr>
<tr>
<td class=main>Gutscheine können, falls sie im Shop angeboten werden, wie normale Artikel gekauft werden. Sobald Sie einen Gutschein gekauft haben und dieser nach erfolgreicher Zahlung freigeschaltet wurde, erscheint der Betrag unter Ihrem Warenkorb. Nun können Sie über den Link " Gutschein versenden " den gewünschten Betrag per E-Mail versenden. </td></tr></tbody></table>
<table cellSpacing=0 cellPadding=0>
<tbody>
<tr>
<td class=main><STRONG>Wie man Gutscheine versendet </STRONG></td></tr>
<tr>
<td class=main>Um einen Gutschein zu versenden, klicken Sie bitte auf den Link "Gutschein versenden" in Ihrem Einkaufskorb. Um einen Gutschein zu versenden, benötigen wir folgende Angaben von Ihnen: Vor- und Nachname des Empfängers. Eine gültige E-Mail Adresse des Empfängers. Den gewünschten Betrag (Sie können auch Teilbeträge Ihres Guthabens versenden). Eine kurze Nachricht an den Empfänger. Bitte überprüfen Sie Ihre Angaben noch einmal vor dem Versenden. Sie haben vor dem Versenden jederzeit die Möglichkeit Ihre Angaben zu korrigieren. </td></tr></tbody></table>
<table cellSpacing=0 cellPadding=0>
<tbody>
<tr>
<td class=main><STRONG>Mit Gutscheinen Einkaufen. </STRONG></td></tr>
<tr>
<td class=main>Sobald Sie über ein Guthaben verfügen, können Sie dieses zum Bezahlen Ihrer Bestellung verwenden. Während des Bestellvorganges haben Sie die Mï¿½glichkeit Ihr Guthaben einzulï¿½sen. Falls das Guthaben unter dem Warenwert liegt müssen Sie Ihre bevorzugte Zahlungsweise für den Differenzbetrag wühlen. übersteigt Ihr Guthaben den Warenwert, steht Ihnen das Restguthaben selbstverständlich für Ihre nächste Bestellung zur Verfügung. </td></tr></tbody></table>
<table cellSpacing=0 cellPadding=0>
<tbody>
<tr>
<td class=main><STRONG>Gutscheine verbuchen. </STRONG></td></tr>
<tr>
<td class=main>Wenn Sie einen Gutschein per E-Mail erhalten haben, können Sie den Betrag wie folgt verbuchen:. <br />1. Klicken Sie auf den in der E-Mail angegebenen Link. Falls Sie noch nicht über ein persönliches Kundenkonto verfügen, haben Sie die Möglichkeit ein Konto zu eröffnen. <br />2. Nachdem Sie ein Produkt in den Warenkorb gelegt haben, können Sie dort Ihren Gutscheincode eingeben.</td></tr></tbody></table>
<table cellSpacing=0 cellPadding=0>
<tbody>
<tr>
<td class=main><STRONG>Falls es zu Problemen kommen sollte: </STRONG></td></tr>
<tr>
<td class=main>Falls es wider Erwarten zu Problemen mit einem Gutschein kommen sollte, kontaktieren Sie uns bitte per E-Mail : you@yourdomain.com. Bitte beschreiben Sie möglichst genau das Problem, wichtige Angaben sind unter anderem: Ihre Kundennummer, der Gutscheincode, Fehlermeldungen des Systems sowie der von Ihnen benutzte Browser. </td></tr></tbody></table>' WHERE `content_manager`.`content_id` = 11;
UPDATE `c2_sc_21`.`content_manager` SET `content_text` = 'Fügen Sie hier Ihre Informationen über Liefer- und Versandkosten ein.' WHERE `content_manager`.`content_id` = 6;
UPDATE `c2_sc_21`.`content_manager` SET `content_title` = 'Privatsphäre und Datenschutz', `content_heading` = 'Privatsphäre und Datenschutz', `content_text` = 'Fügen Sie hier Ihre Informationen über Privatsphäre und Datenschutz ein.', `content_meta_title` = 'Privatsphäre und Datenschutz' WHERE `content_manager`.`content_id` = 7;
UPDATE `c2_sc_21`.`content_manager` SET `content_heading` = 'Allgemeine Geschäftsbedingungen', `content_text` = '<strong>Allgemeine Geschäftsbedingungen<br /></strong><br />Fügen Sie hier Ihre allgemeinen Geschäftsbedingungen ein.<br />1. Geltung<br />2. Angebote<br />3. Preis<br />4. Versand und Gefahrübergang<br />5. Lieferung<br />6. Zahlungsbedingungen<br />7. Eigentumsvorbehalt <br />8. Mängelrügen, Gewährleistung und Schadenersatz<br />9. Kulanzrücknahme / Annahmeverweigerung<br />10. Erfüllungsort und Gerichtsstand<br />11. Schlussbestimmungen' WHERE `content_manager`.`content_id` = 8;
UPDATE `c2_sc_21`.`content_manager` SET `content_text` = 'Fügen Sie hier Ihr Widerrufsrecht ein.' WHERE `content_manager`.`content_id` = 22;
UPDATE `c2_sc_21`.`content_manager` SET `content_text` = '<div class="widerrufsformular-wrapper">
<div class="widerrufsformular-grau">Wenn Sie den Vertrag widerrufen wollen, dann füllen Sie bitte dieses Formular aus und Senden es uns zurück. <br /><br /><br />
<table>
<tbody>
<tr>
<td class="t1"><strong>An:</strong></td>
<td class="t2">Max Mustermann <br /> Musterstraße 1 <br /> 12345 Musterstadt</td>
<td>Telefon: 0123 45678 <br /> Fax: 0123 456789 <br /> E-Mail: max@mustermann.de</td>
</tr>
</tbody>
</table>
</div>
<br />
<p class="nummer1 nummer">1</p>
<p class="nummer2 nummer">2</p>
<p class="nummer3 nummer">3</p>
<p class="nummer4 nummer">4</p>
<p class="nummer5 nummer">5</p>
<p class="nummer6 nummer">6</p>
<p>Hiermit widerrufe(n) ich/wir den von mir/uns abgeschlossenen Vertrag über den Kauf der <br /> folgenden Waren/die Erbringung der folgenden Dienstleistung: <br /><br /> <strong class="blau">Angaben der Ware:</strong></p>
<table class="verbraucherrechte-formular">
<tbody>
<tr>
<td width="70%">
<p>_____ x ________________</p>
<p class="t1">Anzahl | Artikelname</p>
<p>_______________________</p>
<p class="t1">Bestellnummer</p>
<p class="unterstriche">_ _ _ _ , _ _ &euro;</p>
<p class="t1">Gesamtpreis der Ware</p>
<br /><br /> <strong class="blau">Ihre persönlichen Angaben:</strong> <br /><br /><br /><br /><br /><br /><br /><br /> <br style="margin-bottom: 18px;" />
<p class="unterstriche">_ _ . _ _ . _ _ _ _</p>
<p class="t1">Datum</p>
</td>
<td>
<p class="unterstriche">_ _ . _ _ . _ _ _ _</p>
<p class="t1">Bestelldatum</p>
<p class="unterstriche">_ _ . _ _ . _ _ _ _</p>
<p class="t1">Ware erhalten am</p>
<br /><br /><br /><br />
<p>_______________________</p>
<p class="t1">Vorname/Name</p>
<p>_______________________</p>
<p class="t1">Straße und Hausnummer</p>
<p class="unterstriche">_ _ _ _ _</p>
<p class="t1">PLZ</p>
<p>_______________________</p>
<p class="t1">Ort</p>
</td>
</tr>
</tbody>
</table>
</div>' WHERE `content_manager`.`content_id` = 23;