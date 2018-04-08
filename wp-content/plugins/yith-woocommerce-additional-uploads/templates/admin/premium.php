<?php
/**
 * Premium Tab
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Uploads
 * @version 1.0.2
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
?>

<style>
.section{
    margin-left: -20px;
    margin-right: -20px;
    font-family: "Raleway",san-serif;
}
.section h1{
    text-align: center;
    text-transform: uppercase;
    color: #808a97;
    font-size: 35px;
    font-weight: 700;
    line-height: normal;
    display: inline-block;
    width: 100%;
    margin: 50px 0 0;
}
.section ul{
    list-style-type: disc;
    padding-left: 15px;
}
.section:nth-child(even){
    background-color: #fff;
}
.section:nth-child(odd){
    background-color: #f1f1f1;
}
.section .section-title img{
    display: table-cell;
    vertical-align: middle;
    width: auto;
    margin-right: 15px;
}
.section h2,
.section h3 {
    display: inline-block;
    vertical-align: middle;
    padding: 0;
    font-size: 24px;
    font-weight: 700;
    color: #808a97;
    text-transform: uppercase;
}

.section .section-title h2{
    display: table-cell;
    vertical-align: middle;
    line-height: 25px;
}

.section-title{
    display: table;
}

.section h3 {
    font-size: 14px;
    line-height: 28px;
    margin-bottom: 0;
    display: block;
}

.section p{
    font-size: 13px;
    margin: 25px 0;
}
.section ul li{
    margin-bottom: 4px;
}
.landing-container{
    max-width: 750px;
    margin-left: auto;
    margin-right: auto;
    padding: 50px 0 30px;
}
.landing-container:after{
    display: block;
    clear: both;
    content: '';
}
.landing-container .col-1,
.landing-container .col-2{
    float: left;
    box-sizing: border-box;
    padding: 0 15px;
}
.landing-container .col-1 img{
    width: 100%;
}
.landing-container .col-1{
    width: 55%;
}
.landing-container .col-2{
    width: 45%;
}
.premium-cta{
    background-color: #808a97;
    color: #fff;
    border-radius: 6px;
    padding: 20px 15px;
}
.premium-cta:after{
    content: '';
    display: block;
    clear: both;
}
.premium-cta p{
    margin: 7px 0;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    width: 60%;
}
.premium-cta a.button{
    border-radius: 6px;
    height: 60px;
    float: right;
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>upgrade.png) #ff643f no-repeat 13px 13px;
    border-color: #ff643f;
    box-shadow: none;
    outline: none;
    color: #fff;
    position: relative;
    padding: 9px 50px 9px 70px;
}
.premium-cta a.button:hover,
.premium-cta a.button:active,
.premium-cta a.button:focus{
    color: #fff;
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>upgrade.png) #971d00 no-repeat 13px 13px;
    border-color: #971d00;
    box-shadow: none;
    outline: none;
}
.premium-cta a.button:focus{
    top: 1px;
}
.premium-cta a.button span{
    line-height: 13px;
}
.premium-cta a.button .highlight{
    display: block;
    font-size: 20px;
    font-weight: 700;
    line-height: 20px;
}
.premium-cta .highlight{
    text-transform: uppercase;
    background: none;
    font-weight: 800;
    color: #fff;
}

.section.one{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.two{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>02-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.three{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.four{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>04-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.five{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.six{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>06-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.seven{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>07-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.eight{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>08-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.nine{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>09-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.ten{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>10-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.eleven{
    background: url(<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>yith-bg.png) no-repeat #fff; background-position: 85% 75%
}


@media (max-width: 768px) {
    .section{margin: 0}
    .premium-cta p{
        width: 100%;
    }
    .premium-cta{
        text-align: center;
    }
    .premium-cta a.button{
        float: none;
    }
}

@media (max-width: 480px){
    .wrap{
        margin-right: 0;
    }
    .section{
        margin: 0;
    }
    .landing-container .col-1,
    .landing-container .col-2{
        width: 100%;
        padding: 0 15px;
    }
    .section-odd .col-1 {
        float: left;
        margin-right: -100%;
    }
    .section-odd .col-2 {
        float: right;
        margin-top: 65%;
    }
}

@media (max-width: 320px){
    .premium-cta a.button{
        padding: 9px 20px 9px 70px;
    }

    .section .section-title img{
        display: none;
    }
}
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Uploads%2$s to benefit from all features!','yith-woocommerce-additional-uploads'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-additional-uploads');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-additional-uploads');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-woocommerce-additional-uploads');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>01.png" alt="<?php _e( 'Accept or reject','yith-woocommerce-additional-uploads') ?>" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>01-icon.png" alt="icon 01"/>
                    <h2><?php _e('Accept or reject','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Interact with your shop users that have attached a file to products. Verify the quality of the sent images or, the quality of every file you have received, and %1$saccept%2$s or %1$sreject%2$s them. With the second case, users will be free to upload a new file.', 'yith-woocommerce-additional-uploads'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>02-icon.png" alt="icon 02" />
                    <h2><?php _e('Upload by the product','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__(' A more organized and intuitive upload. With the premium version of the plugin, %1$susers can attach a file directly by the product%2$s, in order to let you work freely without any doubts about its correlated item. ', 'yith-woocommerce-additional-uploads'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>02.png" alt="<?php _e( 'Upload by the product','yith-woocommerce-additional-uploads') ?>" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>03.png" alt="<?php _e( 'Attachment customization','yith-woocommerce-additional-uploads') ?>" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'Attachment customization','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Shop products are often different, and you could have the need to request to your users multiple files for order processing. The premium version of the plugin helps you perfectly, configuring %1$sgeneral%2$s or %1$sad hoc%2$s settings for each product of the shop. ', 'yith-woocommerce-additional-uploads'), '<b>', '</b>');?>
                </p>
                <p>
                    <?php _e('And if you want to avoid some products to benefit from this feature, you will just have to click just once to deactivate the plugin\'s features.', 'yith-woocommerce-additional-uploads');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>04-icon.png" alt="icon 04" />
                    <h2><?php _e('File removal','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Very often some of your users may want to change one of the attached files, maybe because they just want something different. Be prepared: purchase the premium version of the plugin and set the %1$sorder status%2$s in which you want to allow users to %1$sremove%2$s attached files. ', 'yith-woocommerce-additional-uploads'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>04.png" alt="<?php _e( 'File removal','yith-woocommerce-additional-uploads') ?>" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>05.png" alt="<?php _e( 'Attached notes','yith-woocommerce-additional-uploads') ?>" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>05-icon.png" alt="icon 05" />
                    <h2><?php _e('Attached notes','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Activate notes for each uploaded file to give your users the freedom to add details for their attachments. In this way, you will have more %1$sinformation%2$s for your work, and the quality will just get better.','yith-woocommerce-additional-uploads' ),'<b>','</b>','<br>' ) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>06-icon.png" alt="icon 06" />
                    <h2><?php _e('Saving path','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Customize the saving path according to your needs. Decide whether to sort your files by %1$sID%2$s or by %1$sorder number%2$s. Two solutions that will let you find easily and quickly the file you are looking for.','yith-woocommerce-additional-uploads'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>06.png" alt="<?php _e( 'Saving path','yith-woocommerce-additional-uploads') ?>" />
            </div>
        </div>
    </div>
    <div class="seven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>07.png" alt="<?php _e( 'Email notification','yith-woocommerce-additional-uploads') ?>" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>07-icon.png" alt="icon 07" />
                    <h2><?php _e('Email notification','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Don\'t forgo the integrated email notification system of the premium version of the plugin. %1$sInform your users about the status of their attachments%2$s, telling them if they have been accepted or rejected, perhaps for inappropriateness of content or low quality. Keep always in touch with them for a top-level service!','yith-woocommerce-additional-uploads'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="eight section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>08-icon.png" alt="icon 08" />
                    <h2><?php _e('User interaction','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Take advantage of the comfortable email system that can easily and %1$squickly send messages%2$s about orders to your shop users. Write your messages and send them from the WooCommerce order detail page. Interact with your users to appear as the best vendor possible.','yith-woocommerce-additional-uploads'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>08.png" alt="<?php _e( 'User interaction','yith-woocommerce-additional-uploads') ?>" />
            </div>
        </div>
    </div>
    <div class="nine section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>09.png" alt="<?php _e( 'Upload pages','yith-woocommerce-additional-uploads') ?>" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>07-icon.png" alt="icon 09" />
                    <h2><?php _e('Upload pages','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('It is up to you choosing when you want your users to upload necessary files, so you can choose where showing the upload button in your shop: in %1$scart%2$s page, in %1$scheckout%2$s page or in %1$s"Thank you"%2$s page, so that your users will always be offered the best purchase conditions.','yith-woocommerce-additional-uploads'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="ten section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>10-icon.png" alt="icon 10" />
                    <h2><?php _e(' Single product','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Every product in your shop is different from the others for features and usage, so this means that not for all products you have to ask your customers for an attachment. This is the reason why the plugin gives you also the opportunity to disable file upload for the specified product or to %1$sset upload rules%2$s for it or for its %1$svariations%2$s.','yith-woocommerce-additional-uploads'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>08.png" alt="<?php _e( 'Single product','yith-woocommerce-additional-uploads') ?>" />
            </div>
        </div>
    </div>
    <div class="eleven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>11.jpg" alt="<?php _e( 'Split products','yith-woocommerce-additional-uploads') ?>" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWAU_ASSETS_IMAGES_URL ?>yith-icon.png" alt="icon 11" />
                    <h2><?php _e('Products split in cart','yith-woocommerce-additional-uploads');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Who know how many times your customers wished to purchase more than one copy of the same product, but customise each of them with different images! Now, you can do that!%3$sThanks to the %1$s"splitting"%2$s option, you will be able to split copies of the same product in more lines and attach different files to each of them. If until now you could do that only by placing different orders, now you can do all with in one order only!','yith-woocommerce-additional-uploads'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Uploads%2$s to benefit from all features!','yith-woocommerce-additional-uploads'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-additional-uploads');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-additional-uploads');?></span>
                </a>
            </div>
        </div>
    </div>
</div>
