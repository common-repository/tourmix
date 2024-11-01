<?php
/**
 * The API key getter form.
 * 
 * @since      1.0.1
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="tourmix-delivery-orders-container tourmix-delivery-api-token-form-container">
    <div style="margin-bottom: 24px;">
        <h1>Beállítások</h1>
        <p>
            Figyelem! Ahhoz, hogy használni tudja a TOURMIX rendelés feladás funkciót meg kell adnia az API kulcsot, melyet a TOURMIX partner fiókjában talál meg!
        </p>

        <p>
            Amennyiben ön még nem partnerünk regisztráljon partnerként a TOURMIX oldalán az alábbi linken: 
            <a href="https://tourmix.delivery/partnerpanel/register" target="_blank">https://tourmix.delivery/partnerpanel/register</a>
        </p>

        <form method="POST" id="api-token-form">
            <label for="api_token">
                API kulcs: 
            </label>
            <input type="text" name="api_token" placeholder="Az ön API kulcsa" data-message="API kulcs kötelező" required>
            <input type="submit" value="Kulcs mentése">
        </form>

        <?php
            if(isset($_POST['api_token'])) {
                $apiToken = str_replace(" ", "", sanitize_text_field($_POST['api_token']));

                if(!TourmixApiHandler::isTokenValid($apiToken)) {
        ?>
                    <div style="margin-top: 10px;color: red;">Az ön által megadott API kulcs hibás!<div>  
        <?php
                }
        }
        ?>
    </div>
    <div>
        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>../imgs/login_screenshot.jpg" alt="" style="width: 100%">
    </div>
</div>