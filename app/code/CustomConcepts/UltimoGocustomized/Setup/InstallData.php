<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_UltimoGocustomized
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\UltimoGocustomized\Setup;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var BlockInterfaceFactory
     */
    private $blockInterfaceFactory;
    
    protected $configWriter;

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockInterfaceFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
            
    ) {
        $this->blockRepository = $blockRepository;

        // Here we need to use a factory for the \Magento\Cms\Api\Data\BlockInterface
        // This is because we will need to create a new instance of a CMS block rather than
        // being "stuck" in a Singleton.
        $this->blockInterfaceFactory = $blockInterfaceFactory;
        
        $this->configWriter = $configWriter;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * 
         * Banner configuration
         */
        
        $this->configWriter->save('ultraslideshow/general/effect',  NULL, 'default', 0);
        $this->configWriter->save('ultraslideshow/general/timeout',  '10000', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/speed',  '200', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/auto_speed',  '500', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/smooth_height',  '0', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/pause',  '1', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/loop',  '1', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/hide',  '0', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/blocks',  'home_slider_1,home_slider_2,home_slider_3', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/position1',  '0', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/position2',  '1', 'default', 0);
        $this->configWriter->save('ultraslideshow/general/margin_top',  NULL, 'default', 0);
        $this->configWriter->save('ultraslideshow/general/margin_bottom',  NULL, 'default', 0);
        $this->configWriter->save('ultraslideshow/banners/position',  'right', 'default', 0);
        $this->configWriter->save('ultraslideshow/banners/hide',  '0', 'default', 0);
        $this->configWriter->save('ultraslideshow/banners/banners',  NULL, 'default', 0);
        $this->configWriter->save('ultraslideshow/navigation/pagination',  NULL, 'default', 0);
        $this->configWriter->save('ultraslideshow/navigation/pagination_position',  'bottom-centered', 'default', 0);
        $this->configWriter->save('ultraslideshow/banners/gutter',  '1', 'default', 0);
        $this->configWriter->save('ultraslideshow/navigation/nav_buttons',  '1', 'default', 0);
        
        $this->configWriter->save('ultramegamenu/mainmenu/home',  '0', 'default', 0);
        $this->configWriter->save('ultramegamenu/mainmenu/centered',  '1', 'default', 0);
        
        /**
         * 
         * Banner configuration
         */
        
        $this->configWriter->save('design/theme/theme_id', '6', 'stores', 1);
        $this->configWriter->save('theme_settings/header/left_column', '3', 'default', 0);
        $this->configWriter->save('theme_settings/header/central_column', '3', 'default', 0);
        $this->configWriter->save('theme_settings/header/right_column', '6', 'default', 0);
        $this->configWriter->save('theme_settings/header/logo_position', 'primLeftCol', 'default', 0);
        $this->configWriter->save('theme_settings/header/search_position', 'primRightCol', 'default', 0);
        $this->configWriter->save('theme_settings/header/search_in_user_menu_position', '1', 'default', 0);
        $this->configWriter->save('theme_settings/header/account_links_position', 'topRight', 'default', 0);
        $this->configWriter->save('theme_settings/header/top_links', '1', 'default', 0);
        $this->configWriter->save('theme_settings/header/user_menu_position', 'primRightCol', 'default', 0);
        $this->configWriter->save('theme_settings/header/toplinks_break_position', '0', 'default', 0);
        $this->configWriter->save('theme_settings/header/main_menu_position', 'menuContainer', 'default', 0);
        $this->configWriter->save('theme_settings/header/cart_position', 'primRightCol', 'default', 0);
        $this->configWriter->save('theme_settings/header/cart_label', '0', 'default', 0);
        $this->configWriter->save('theme_settings/header/currency_switcher_position', 'topLeft', 'default', 0);
        $this->configWriter->save('theme_settings/header/lang_switcher_position', 'topLeft', 'default', 0);
        $this->configWriter->save('theme_settings/header/sticky', '1', 'default', 0);
        $this->configWriter->save('theme_settings/header/sticky_full_width', '0', 'default', 0);
        $this->configWriter->save('theme_settings/header/sticky_logo', '0', 'default', 0);
        $this->configWriter->save('theme_settings/header/mobile_move_switchers', '0', 'default', 0);
        $this->configWriter->save('theme_settings/header/mode', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category/aspect_ratio', '0', 'default', 0);
        $this->configWriter->save('theme_settings/category/image_width', '295', 'default', 0);
        $this->configWriter->save('theme_settings/category/alt_image', '0', 'default', 0);
        $this->configWriter->save('theme_settings/category/alt_image_column', 'label', 'default', 0);
        $this->configWriter->save('theme_settings/category/alt_image_column_value', '2', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/column_count', '3', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/column_count_768', '3', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/column_count_640', '2', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/column_count_480', '2', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/equal_height', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/hover_effect', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/disable_hover_effect', '320', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/hide_addto_links', '480', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/centered', '0', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/elements_size', NULL, 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/display_name', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/display_name_single_line', '0', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/display_price', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/display_rating', '2', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/display_addtocart', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/display_addtolinks', '2', 'default', 0);
        $this->configWriter->save('theme_settings/category_grid/addtolinks_position', '1', 'default', 0);
        $this->configWriter->save('theme_settings/category_list/hover_effect', '0', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/image_column', '4', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/primary_column', '8', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/secondary_column', NULL, 'default', 0);
        $this->configWriter->save('theme_settings/product_page/lower_primary_column', '12', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/lower_secondary_column', '12', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/container2_column', '9', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/collateral_position', 'lowerPrimCol_1', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/tabs', '1', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/tabs_style', 'style-luma', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/related_position', 'primCol_1', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/related_template', 'product/list/items.phtml', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/related_count', '4', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/related_breakpoints', '[0, 1], [320, 2], [480, 3], [768, 1]', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/related_timeout', '6000', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/related_checkbox', '0', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/upsell_position', 'primCol_1', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/upsell_template', 'product/list/items.phtml', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/upsell_count', NULL, 'default', 0);
        $this->configWriter->save('theme_settings/product_page/upsell_breakpoints', '[0, 1], [320, 2], [480, 3], [992, 4], [1200, 5]', 'default', 0);
        $this->configWriter->save('theme_settings/product_page/upsell_timeout', NULL, 'default', 0);
        $this->configWriter->save('theme_settings/product_page/brand_position', 'secCol_1', 'default', 0);
        $this->configWriter->save('theme_settings/product_labels/new', '1', 'default', 0);
        $this->configWriter->save('theme_settings/product_labels/sale', '1', 'default', 0);
        $this->configWriter->save('theme_settings/footer/links_column_auto_width', '1', 'default', 0);
        $this->configWriter->save('theme_settings/footer/footer_links', '0', 'default', 0);
        $this->configWriter->save('theme_settings/footer/newsletter', '1', 'default', 0);
        $this->configWriter->save('theme_settings/footer/store_switcher', '0', 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/timeout', NULL, 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/speed', '200', 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/auto_speed', '500', 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/pause', '1', 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/loop', '1', 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/lazy', '1', 'default', 0);
        $this->configWriter->save('theme_settings/product_slider/keep_aspect_ratio', '0', 'default', 0);
        $this->configWriter->save('theme_settings/rsnippets/enable_product', '1', 'default', 0);
        $this->configWriter->save('theme_settings/rsnippets/price_incl_tax', '0', 'default', 0);
        $this->configWriter->save('theme_settings/install/overwrite_blocks', '0', 'default', 0);
        $this->configWriter->save('theme_settings/install/overwrite_pages', '0', 'default', 0);
        $this->configWriter->save('theme_settings/category/image_height', NULL, 'default', 0);
        $this->configWriter->save('theme_settings/layered_navigation/category_filter', '1', 'default', 0);
        $this->configWriter->save('theme_settings/gallery/magnifier', '1', 'default', 0);
        $this->configWriter->save('theme_settings/gallery/magnifier_width', '300', 'default', 0);
        $this->configWriter->save('theme_settings/gallery/magnifier_height', '300', 'default', 0);
        $this->configWriter->save('theme_settings/install/demo_number', '1', 'default', 0);
        
        
        
        
        
        
        $this->createCmsBlockHeaderTopBar();
        $this->createCmsBlockBannerSlider1();
        $this->createCmsBlockBannerSlider2();
        $this->createCmsBlockBannerSlider3();
        $this->createCmsBlockFooterPrivacyLinksGCUS();
        $this->createCmsBlockFooterInfoGCUS();
        $this->createCmsBlockFooterFaqGCUS();
        $this->createCmsBlockFooterMyaccountGCUS();
        $this->createCmsBlockFooterSocialLinksGCUS();
        $this->createCmsBlockFooterPaymentContainerGCUS();
        $this->createCmsBlockHomepageNewsletter();
        $this->createCmsBlockHomepageSeoBlock();
        $this->createCmsBlockHomepageAboutUs();
        $this->createCmsBlockHomepageFindYourDevice();
        
        $this->createCmsPage();

    }
    
    /**
     * Create a CMS Page
     * Home Page
     */
    public function createCmsPage()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $block = $objectManager->create('Magento\Cms\Model\Page');
            $block->load('home', 'identifier');
            $block->setPageLayout('1column-full-width');
            $block->setContent('');
            $block->save();

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Create a CMS block
     * Header Top Bar
     */
    public function createCmsBlock($identifier,$title,$content,$store_id = 0 )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $block = $objectManager->create('Magento\Cms\Model\Block');
            $block->setStoreId($store_id); // store for block you want to update
            $block->load($identifier, 'identifier');
	    if (!$block->getId()) {
                $block->setIdentifier($identifier);
                $block->setTitle($title);
                $block->setIsActive(1);
                $block->setStores($store_id);
                $block->setContent($content);
                $block->save();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Create a CMS block
     * Header Top Bar
     */
    public function createCmsBlockHeaderTopBar()
    {
        $identifier = 'header_top_bar';
        $title = 'Header Top Bar';
        $content = '<div class="header-top-bar">
                    <ul>
                        <li class="grid12-4"><span class="personalise-your-gadgets"></span>            
                            <p><b>PERSONALIZE YOUR GADGETS</b><label>Make your devices truly your own </label></p>
                        </li>
                        <li class="grid12-4"><span class="same-day-shipping"></span>            
                            <p><b>SAME DAY SHIPPING </b><label>For orders placed until 16:00 </label></p>
                        </li>
                        <li class="grid12-4"><span class="no-fuss-payment"></span>                        
                            <p><b>NO FUSS PAYMENT </b><label> Many payment options available </label></p>

                        </li>
                    </ul>
                </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Banner Slider 1
     */
    public function createCmsBlockBannerSlider1()
    {
        $identifier = 'home_slider_1';
        $title = 'Home : Banner Slider 1';
        $content = '<div class="item">
                        <div class="ban">
                            <a href="category/phonecases">
                                <img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/banner/1-desktopnotext.jpg" class="mobile-hide" alt="Personalized phone cases">
                                <img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/banner/us/1-mob.jpg" class="mobile-show" alt="Personalized phone cases">
                                <!-- Position the horizontally with bannertextleft or bannertextcenter or bannertextright and vertically with bannertexttop or bannertextmiddle or bannertextbottom -->
                                <!-- Always create a banner for mobile and for desktop!!!! -->
                                <div class="bannertext bannertextright bannertextmiddle mobile-hide">
                                     <h1 class="bannerboldtext">Make your<br>own design</h1>
                                    <h2 class="bannersmalltext">Create a unique design for your smartphone or tablet</h2>
                                </div>
                            </a>
                            <div class="cap cap-push-up-15 cap-push-right-5 cap-no-bg cap-text-bg cap-text-bg-light-1" style="left:auto; right:4%; bottom:10%; top:auto;">
                                <a href="category/phonecases"></a>
                                <a class="button" href="category/phonecases">PERSONALIZE YOUR CASE</a>
                            </div>
                        </div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Banner Slider 2
     */
    public function createCmsBlockBannerSlider2()
    {
        $identifier = 'home_slider_2';
        $title = 'Home : Banner Slider 2';
        $content = '<div class="item">
                        <div class="ban">
                            <a href="category/tabletcases">
                                <img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/banner/banner-ipad-desktop.jpg" class="mobile-hide" alt="Design your own case">
                                <img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/banner/us/us-ipad-mobile.jpg" class="mobile-show" alt="Design your own case">
                                <!-- Position the horizontally with bannertextleft or bannertextcenter or bannertextright and vertically with bannertexttop or bannertextmiddle or bannertextbottom -->
                                <!-- Always create a banner for mobile and for desktop!!!! -->
                                <div class="bannertext bannertextleft bannertextmiddle mobile-hide">
                                     <h1 class="bannerboldtext" style="font-size:2.4vw;">STYLE &amp; PROTECTION FOR YOUR IPAD</h1>
                                    <h2 class="bannersmalltext">A cover, a stand and a unique style in one smartcover</h2>
                                </div>
                            </a>
                            <div class="cap cap-push-up-15 cap-push-right-5 cap-no-bg cap-text-bg cap-text-bg-light-1" style="left:4%; right:auto; bottom:10%; top:auto;">
                                <a href="category/tabletcases"></a>
                                <a class="button" href="category/tabletcases">DESIGN YOUR SMARTCOVER</a>
                            </div>
                        </div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Banner Slider 3
     */
    public function createCmsBlockBannerSlider3()
    {
        $identifier = 'home_slider_3';
        $title = 'Home : Banner Slider 3';
        $content = '<div class="item">
                        <div class="ban">
                            <a href="category/accessories">
                                <img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/banner/2-desktopnotext.jpg" class="mobile-hide" alt="Design your own case">
                                <img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/banner/us/2-mob.jpg" class="mobile-show" alt="Design your own case">
                                <!-- Position the horizontally with bannertextleft or bannertextcenter or bannertextright and vertically with bannertexttop or bannertextmiddle or bannertextbottom -->
                                <!-- Always create a banner for mobile and for desktop!!!! -->
                                <div class="bannertext bannertextleft bannertextmiddle mobile-hide">
                                     <h1 class="bannerboldtext">PERSONALIZE YOUR GADGETS</h1>
                                    <h2 class="bannersmalltext">Your design on a bluetooth speaker or power bank</h2>
                                </div>
                            </a>
                            <div class="cap cap-push-up-15 cap-push-right-5 cap-no-bg cap-text-bg cap-text-bg-light-1" style="left:4%; right:auto; bottom:10%; top:auto;">
                                <a href="category/accessories"></a>
                                <a class="button" href="category/accessories">GET STARTED</a>
                            </div>
                        </div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    
    /**
     * Create a CMS block
     * Footer Privacy Links GCUS
     */
    public function createCmsBlockFooterPrivacyLinksGCUS()
    {
        $identifier = 'footer_privacy_links';
        $title = 'Footer Privacy Links GCUS';
        $content = '<ul>
                        <li><a href="{{store url="terms-conditions"}}">Terms &amp; Conditions</a></li>
                        <li><a href="{{store url="privacy_policy"}}">Privacy policy</a></li>
                    </ul>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Footer - Info GCUS
     */
    public function createCmsBlockFooterInfoGCUS()
    {
        $identifier = 'block_footer_info';
        $title = 'Footer - Info GCUS';
        $content = '<h4 class="footer-title">INFO &amp; CONTACT</h4>
                    <ul>
                        <li><a href="{{store url="business-to-business"}}">B2B</a></li>
                        <li><a href="{{store url="about-gocustomized"}}">About us</a></li>
                        <li><a href="{{store url="press-releases-gocustomized"}}">Press releases</a></li>
                        <li><a href="{{store url="blog"}}">Blog</a></li>
                        <li><a href="{{store url="careers"}}">Careers</a></li>
                        <li><a href="{{store url="contact-us"}}">Contact us</a></li>
                    </ul>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Footer - FAQ GCUS
     */
    public function createCmsBlockFooterFaqGCUS()
    {
        $identifier = 'block_footer_faq';
        $title = 'Footer - FAQ GCUS';
        $content = '<h4 class="footer-title">HELP</h4>
                    <ul>
                        <li><a href="{{store url="productsoverview"}}">Design dimensions</a></li>
                        <li><a href="{{store url="how-to-create-a-custom-phone-case"}}">How to customize your phone case</a></li>
                        <li><a href="{{store url="help"}}">Frequently asked questions</a></li>
                    </ul>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Footer - MY ACCOUNT GCUS
     */
    public function createCmsBlockFooterMyaccountGCUS()
    {
        $identifier = 'footer_block_myaccount';
        $title = 'Footer - MY ACCOUNT GCUS';
        $content = '<h4 class="footer-title">MY ACCOUNT</h4>
                    <ul>
                        <li><a href="{{store url="customer/account/"}}">Account overview </a></li>
                        <li><a href="{{store url="sales/order/history/"}}">Order overview</a></li>
                        <li><a href="{{store url="customer/account/edit/"}}">Change your account details</a></li>
                    </ul>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    
    /**
     * Create a CMS block
     * Footer - Social Links GCUS
     */
    public function createCmsBlockFooterSocialLinksGCUS()
    {
        $identifier = 'footer_block_sociallinks';
        $title = 'Footer - Social Links GCUS';
        $content = '<ul class="social">
                        <li>
                            <a href="https://www.instagram.com/gocustomized/" target="_blank" title="Instgram"><i class="ic-instagram"></i></a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/GoCustomized/" target="_blank" title="Facebook"><i class="ic-facebook"></i></a>
                        </li>
                        <li>
                            <a href="https://twitter.com/gocustomized" target="_blank" title="Twitter"><i class="ic-twitter"></i></a>
                        </li>
                        <li>
                            <a href="https://www.pinterest.com/gocustomized/" target="_blank" title="Pinterest"><i class="ic-pinterest"></i></a>
                        </li>
                    </ul>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Footer Payment Container GCUS
     */
    public function createCmsBlockFooterPaymentContainerGCUS()
    {
        $identifier = 'footer_payment_container';
        $title = 'Footer Payment Container GCUS';
        $content = '<div class="footer-payment-container container ">
                        <div class="half first">
                    <span>Payment Methods</span> <p><img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/EN/payment-US.png" alt=""></p>
                        </div>
                        <div class="half right-text last"><span>Shipping Partner</span> <p><img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/EN/shipping-US.png" alt=""></p></div>
                    <div class="clearfix"></div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    
    /**
     * Create a CMS block
     * Home page :: Newsletter
     */
    public function createCmsBlockHomepageNewsletter()
    {
        $identifier = 'home_newsletter';
        $title = 'Home page :: Newsletter';
        $content = '<div class="block-subscribe margin-tb-30">
                        <div class="container">
                            <div class="grid12-4 promotional-text">
                                <p>SUBSCRIBE TO OUR NEWSLETTER
                    - 5% OFF YOUR NEXT ORDER!</p>        </div>
                            <div class="grid12-4 subscribe-form">
                                <div id="subscribe-form" class="clearer">
                        <form action="https://www.gocustomized.com/newsletter/subscriber/new/" method="post" id="newsletter-validate-detail">
                            <div>
                                <label for="newsletter">Newsletter</label>
                                <div class="input-box">
                                    <input name="email" id="newsletter" title="Sign up for our newsletter" class="input-text required-entry validate-email" type="text">
                                </div>
                                <button type="submit" title="Subscribe" class="button btn-inline"><span><span>Subscribe</span></span></button>
                            </div>
                        </form>
                    </div>
                    <script type="text/javascript">
                    //
                        var newsletterSubscriberFormDetail = new VarienForm(\'newsletter-validate-detail\');
                        new Varien.searchForm(\'newsletter-validate-detail\', \'newsletter\', \'Enter your email address\');
                    //
                    </script>
                            </div>
                            <div class="grid12-4 social-links">
                                <ul>
                        <li>
                            <a href="https://www.instagram.com/gocustomized/" target="_blank" title="Instgram"><i class="ic-instagram"></i></a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/GoCustomized/" target="_blank" title="Facebook"><i class="ic-facebook"></i></a>
                        </li>
                        <li>
                            <a href="https://twitter.com/gocustomized" target="_blank" title="Twitter"><i class="ic-twitter"></i></a>
                        </li>
                        <li>
                            <a href="https://www.pinterest.com/gocustomized/" target="_blank" title="Pinterest"><i class="ic-pinterest"></i></a>
                        </li>
                    </ul>        </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    
    /**
     * Create a CMS block
     * Home Page : Seo Block
     */
    public function createCmsBlockHomepageSeoBlock()
    {
        $identifier = 'home_seo_block';
        $title = 'Home Page : Seo Block';
        $content = '<div class="block-graybg seo-block">
                        <div class="block-content margin-top-30 container padding-top-30 padding-bottom-30">
                        <div class="half first"><h1 style="font-size: 1.1rem;margin:0 0 10px 0;padding:0;text-transform:initial">Make your own custom phone case</h1><p> Are you looking for original cellphone or tablet cases? Then you’ve come to the right place here at GoCustomized. Almost everyone these days has generic and identical smartphones, so why not make yours unique? Protecting your cellphone is important, but we here at GoCustomized take it a step further by allowing you to create a custom phone case with a picture!</p><br><p>We offer a wide variety of custom phone cases for almost all smartphones. You can make a custom phone case for the iPhone X, iphone 8, 7, 7 Plus, iPhone 6(S) and 6(S) Plus, iPhone SE and 5S, the Samsung Galaxy S6 and S7, S8 edge, the iPad Air and iPad Pro, and additionally HTC, Huawei and Sony phones. You can design your own phone case with a beautiful picture and a heartwarming text, special date or name of a loved one. How you make your customized phone case is entirely up to you! The best part of it all is that you can stand out of the crowd with custom phone cases.</p><h1 style="font-size: 1.1rem;margin:0 0 10px 0;padding:30px 0 0 0;text-transform:initial">iPad cases with pictures</h1><p>In addition to a phone case design, you can make a magnificent case for your tablet! We offer complete unique cases for the best tablets out there, such as the iPad Pro, iPad Air and iPad Mini. For our iPad case lineup, we offer an optional smart case that will protect the back of your device, which can also be customized with your own personal design. In addition to iPad smart covers, hard cases and soft cases, you can design an iPad sleeve! This durable sleeve provides reliable padding and your image is printed on both sides! Personalised tablet covers are becoming very trendy and ensure that your tablet is better protected on the go! So go and make your custom iPad case now!</p></div>

                        <div class="half last"><h1 style="font-size: 1.1rem;margin:0 0 10px 0;padding:0;text-transform:initial">Large variety of case types</h1><p>We try our best to offer all sorts of custom phone cases and covers. Whether you’re looking for a customized phone case or a custom tablet case, you will be able to find a variety of case types, such as slim cases, soft cases, wallet cases, smart cases, and smart covers. Making a slim case cover will provide your phone with a hard nylon case that protects all corners and is stylized with your very own photo or design. This photo is printed on the back of the case and sometimes even on the sides. A soft case is possible too, if you’re interested in a more rubbery and flexible type of case! High resolution images will be printed on the back of your phone case and are scratch resistant.</p><br><p>Want something more original? Then create your very own wallet case with a design or photo on the front or back of the case. We also offer one of the most innovative and creative custom phone cases on the market in the wooden iPhone case, which can have designs specifically engraved into the back of the smartphone. So when you design your very own custom phone case, be sure to choose the right one that reflects you best! With GoCustomized, creating custom phone cases has never been so easy and fun!</p><br><h1 style="font-size: 1.1rem;margin:0 0 10px 0;padding:0;text-transform:initial">Design a case for yourself or for a gift</h1><p>A personalized phone case is also great for a unique and original gift. The picture would give it a very personal touch and it can be uploaded directly from your PC, Instagram or Facebook! Need some inspiration? Then you can make your own with one of our designs. It’s incredibly easy to design your own phone case and can be done quickly!</p></div>
                        <div class="clearfix">&nbsp;</div>
                        </div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    /**
     * Create a CMS block
     * Home Page :: About Us
     */
    public function createCmsBlockHomepageAboutUs()
    {
        $identifier = 'about_us';
        $title = 'Home Page :: About Us';
        $content = '<div class="block block-about-us padding-bottom-30">
                        <div class="container">
                            <div class="section-title border-bottom">Over ons</div>
                        </div>
                        <div class="aboutus-img"></div>
                        <div class="block-content margin-top-30 container padding-top-30">
                            <div class="half first">GoCustomized is a young e-commerce start-up situated in the heart of Amsterdam, The Netherlands. We focus primarily on the gadget accessories market with our unique and original personalised products. The world of tablets and smartphones has become a bland, expressionless environment where each gadget is but a mimic of its predecessor. We believe we can bring personality and uniqueness back to your favorite gadget in a simple, inexpensive way.
                               <span class="mobile-hide"><br>      Comon...Give me something new and exciting! That was our thought exactly back in 2012 when we set forth on our mission to give you a means to give your gadget a unique and fresh new look. It all started in May 2012 with the release of our first, in-house developed, customised product: The iPhone 4S Backcover. The backcover was a complete customisable glass replacement for the back of the iPhone 4S. It was our flag ship and an immediate success. </span></div>

                            <div class="half last mobile-hide">A Master of personalized printing. Here at GoCustomized we have extensive knowledge of the ins and outs of printing. We specialize in ensuring that the materials we use are in perfect combination with the print technique applied. We have the ability to ensure the best results on whichever type, shape or material case you desire. We can therefore always guarantee a perfect finish.<br><br>
                                We’re not done yet! GoCustomized is always on the move! We have a passionate team that is search far and wide for unique new cases for each and every smartphone or tablet out there. Be it bamboo, flip, wallet or mirror cases for the newest Samsung or iPhone models, we are always one step ahead of the competition. We rigorously test each and every new product before making it available to you to ensure that you get the best of the best and nothing less. </div>
                            <p class="clearfix readmore right-link">
                                <a href="about-gocustomized" class="read-more">
                                    MORE ABOUT US
                                </a>
                            </p>
                        </div>
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    
    /**
     * Create a CMS block
     * Home page : FIND YOUR DEVICE
     */
    public function createCmsBlockHomepageFindYourDevice()
    {
        $identifier = 'find_your_device';
        $title = 'Home page : FIND YOUR DEVICE';
        $content = '<div class="category-section padding-tb-30 container">
                        {{block class="Infortis\Base\Block\Product\ProductList\Featured"
                        template="product/list_featured_slider.phtml" category_id="2" product_count="12"
                        hide_button="1" block_name="FIND YOUR DEVICE"}}
                    </div>';
        $this->createCmsBlock($identifier,$title,$content);
    }
    
    
}
