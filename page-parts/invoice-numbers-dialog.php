<?php
/**
 * This is the layout of the transfer dialog. This dialog is appears when the user cliks to a transfer button.
 * 
 * @since      1.1.2
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="tourmix-dialog-container" id="tourmix-invoice-number-dialog-container" style="display: none;">
    <div class="tourmix-dialog" style="scale: 0;">
        <span class="tourmix-dialog-close-button" id="tourmix-invoice-number-dialog-close-bt">✕</span>
        <div class="tourmix-dialog-message">
            <h2>Utánvétes rendelések számla száma:</h2>

            <p>
                Az utánvétes rendelésekhez meg tudja adni a hozzájuk tartozó számla számokat de ez nem kötelező. 
            </p>

            <p>
                Amennyiben nem szeretné megadni hagyja üresen a beviteli mezőt és kattintson a Feladás gombra.
            </p>
        </div>

        <fieldset class="tourmix-dialog-inputs-frame" id="tourmix-cod-order-dialog-inputs-frame">
            <legend>Adja meg a rendelésekhez tartozó számla számokat</legend>

            <table class="cod_orders_table">
                <thead>
                    <th>
                        Rendelés/Csomagkód
                    </th>
                    <th>
                        Ár
                    </th>
                    <th>
                        Számla száma
                    </th>
                </thead>
                <tbody id="cod-orders-table-body">
                    <tr>
                        <td>
                            #103 Teszt Termek
                        </td>
                        <td>
                            1402,00 Ft
                        </td>
                        <td>
                            <input type="text" class="invoice-number-inputs" data-order-id="103" placeholder="Számla száma">
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>

        <div class="tourmix-error-msg" id="invoice-number-error-msg" style="display: none;">
            A számla számok megadása kötelező!
        </div>

        <div>
            <input type="button" class="tourmix-primary-button" id="cod-order-submit" value="Feladás">
        </div>
    </div>
</div>