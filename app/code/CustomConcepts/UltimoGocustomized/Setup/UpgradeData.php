<?php

namespace CustomConcepts\UltimoGocustomized\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface {

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
    BlockRepositoryInterface $blockRepository, BlockInterfaceFactory $blockInterfaceFactory, \Magento\Framework\App\Config\Storage\WriterInterface $configWriter, EavSetupFactory $eavSetupFactory
    ) {
        $this->blockRepository = $blockRepository;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
        $this->configWriter = $configWriter;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            /* color configuration changes */
            $this->configWriter->save('theme_design/colors/color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/link_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/link_hover_color', '#000', 'default', '0');
            $this->configWriter->save('theme_design/colors/button_bg_color', '#eee', 'default', '0');
            $this->configWriter->save('theme_design/colors/button_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/button_hover_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/colors/button_hover_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/button_active_bg_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/button_active_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/tool_icon_bg_color', '#f5f5f5', 'default', '0');
            $this->configWriter->save('theme_design/colors/tool_icon_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/tool_icon_hover_bg_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/tool_icon_hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/tool_icon_active_bg_color', '#e5e5e5', 'default', '0');
            $this->configWriter->save('theme_design/colors/tool_icon_active_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/icon_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/colors/icon_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/icon_hover_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/colors/icon_hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/social_icon_bg_color', '#bbb', 'default', '0');
            $this->configWriter->save('theme_design/colors/social_icon_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/social_icon_hover_bg_color', '#ff7214', 'default', '0');
            $this->configWriter->save('theme_design/colors/social_icon_hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/ban_cap_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/colors/important_link_hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/important_link_hover_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/colors/label_new_bg_color', 'rgba(91, 210, 236, 0.85)', 'default', '0');
            $this->configWriter->save('theme_design/colors/label_new_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/label_sale_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/colors/label_sale_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/label_custom_bg_color', 'rgba(146, 210, 19, 0.85)', 'default', '0');
            $this->configWriter->save('theme_design/colors/label_custom_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/colors/price_color', '#000', 'default', '0');
            $this->configWriter->save('theme_design/colors/rating_active_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/colors/additional_bg_color', 'f5f5f5', 'default', '0');
            /* font configuration changes */
            $this->configWriter->save('theme_design/font/body_font_family', NULL, 'default', '0');
            $this->configWriter->save('theme_design/font/body_font_size', '14', 'default', '0');
            $this->configWriter->save('theme_design/font/fallback_body_font_stack', 'Lato\'\, sans-serif', 'default', '0');
            $this->configWriter->save('theme_design/font/headings_font_family', NULL, 'default', '0');
            $this->configWriter->save('theme_design/font/headings_font_weight', '400', 'default', '0');
            /* menu configuration changes */
            $this->configWriter->save('theme_design/nav/outer_bg_color', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/padding_top', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/padding_bottom', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/inner_bg_color', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/content_padding_top', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/content_padding_bottom', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/content_padding_side', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/line_height', '50', 'default', '0');
            $this->configWriter->save('theme_design/nav/border', '5', 'default', '0');
            $this->configWriter->save('theme_design/nav/border_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/bg_color', '#f8f8f8', 'default', '0');
            $this->configWriter->save('theme_design/nav/color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/hover_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/nav/hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/active_bg_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/active_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/sticky_item_bg_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/sticky_item_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/sticky_border', '0', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_shadow', '1', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_bg_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_item_hover_bg_color', '#F0F8FF', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_link_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_link_hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_border_top', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_border_top_color', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/dropdown_border_all_levels', '0', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_bg_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_hover_bg_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_hover_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_active_bg_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_active_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_link_separator_color', '#eee', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_shadow', '1', 'default', '0');
            $this->configWriter->save('theme_design/nav/level1_font_family', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/level1_font_size', '16', 'default', '0');
            $this->configWriter->save('theme_design/nav/level1_font_weight', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/level1_font_uppercase', '1', 'default', '0');
            $this->configWriter->save('theme_design/nav/level2_font_family', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/level2_font_size', '16', 'default', '0');
            $this->configWriter->save('theme_design/nav/level2_font_weight', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/level2_font_uppercase', '0', 'default', '0');
            $this->configWriter->save('theme_design/nav/mega_lev1_font_family', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/mega_lev1_font_size', '16', 'default', '0');
            $this->configWriter->save('theme_design/nav/mega_lev1_font_weight', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/mega_lev1_font_uppercase', '1', 'default', '0');
            $this->configWriter->save('theme_design/nav/mega_lev2_font_size', '14', 'default', '0');
            $this->configWriter->save('theme_design/nav/mega_lev2_font_uppercase', '0', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_level1_font_family', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_level1_font_size', '18', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_level1_font_weight', NULL, 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_level1_font_uppercase', '1', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_level2_font_size', '18', 'default', '0');
            $this->configWriter->save('theme_design/nav/mobile_level2_font_uppercase', '0', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_line_height', '36', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_bg_color', '#f8f8f8', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_hover_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_hover_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_active_bg_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_item_active_color', '#f5f5f5', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_shadow', '1', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_trigger_bg_color', '#f7f7f7', 'default', '0');
            $this->configWriter->save('theme_design/nav/vert_trigger_color', '#333', 'default', '0');
            $this->configWriter->save('theme_design/nav/label_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/nav/label_color', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/label_bg_color2', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/nav/label_color2', '#fff', 'default', '0');
            $this->configWriter->save('theme_design/nav/label_hover_bg_color', '#ed6d4f', 'default', '0');
            $this->configWriter->save('theme_design/nav/label_hover_color', '#fff', 'default', '0');

            $this->updateCmsBlockHeaderTopBar();
            $this->updateCmsBlockBannerSlider1();
            $this->updateCmsBlockBannerSlider2();
            $this->updateCmsBlockBannerSlider3();
            $this->updateCmsBlockFooterPrivacyLinksGCUS();
            $this->updateCmsBlockFooterInfoGCUS();
            $this->updateCmsBlockFooterFaqGCUS();
            $this->updateCmsBlockFooterMyaccountGCUS();
            $this->updateCmsBlockFooterSocialLinksGCUS();
            $this->updateCmsBlockFooterPaymentContainerGCUS();
            $this->updateCmsBlockHomepageNewsletter();
            $this->updateCmsBlockHomepageSeoBlock();
            $this->updateCmsBlockHomepageAboutUs();
            $this->updateCmsBlockHomepageFindYourDevice();
            $this->updateCmsBlockHomepageBestSeller();

            $this->updateCmsPage();
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Category::ENTITY, 'bottom_description', [
                'type' => 'text',
                'label' => 'Bottom Description',
                'input' => 'textarea',
                'sort_order' => 100,
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => '',
                'backend' => ''
                    ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->updateCmsBlockHeaderTopBar();
            $this->updateCmsBlockHomepageReview();
            $this->updateCmsBlockB2Bmobile();
            $this->updateCmsBlockAboutsectionMobile();
            $this->updateCmsBlockFooterPaymentContainerGCUS();
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'how_to_make_case', [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'HOW DO I MAKE MY OWN CUSTOM CASE',
                    'input' => 'textarea',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                    ]
            );
            $this->updateCmsBlockB2BRequestform();
        }
    }

    /**
     * Update a CMS Page
     * Home Page
     */
    public function updateCmsPage() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $block = $objectManager->create('Magento\Cms\Model\Page');
            $block->load('home', 'identifier');
            $block->setPageLayout('1column-full-width');
            $block->setContent('');
            $block->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Update a CMS block
     * Header Top Bar
     */
    public function updateCmsBlock($identifier, $title, $content, $store_id = 0) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $block = $objectManager->create('Magento\Cms\Model\Block');
            $block->setStoreId($store_id); // store for block you want to update
            $block->load($identifier, 'identifier');
            if(!$block->getId()){
                $block->setIdentifier($identifier);
                $block->setTitle($title);
                $block->setIsActive(1);
                $block->setStores($store_id);
                $block->setContent($content);
                $block->save();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Update a CMS block
     * Header Top Bar
     */
    public function updateCmsBlockHeaderTopBar() {
        $identifier = 'header_top_bar';
        $title = 'Header Top Bar';
        $content = '<div class="header-top-bar">
                    <ul class="header-top-bar-lists clearfix">
                        <li class="grid12-4"><span class="personalise-your-gadgets"></span>            
                               <p>PERSONLIZE YOU GADGETS
                                  <label>Make your devices truly your own</label>
                               </p>
                        </li>
                        <li class="grid12-4">
                        <span class="same-day-shipping"></span>
                            <p>SAME DAY SHIPPING
                               <label>For orders placed until 16:00</label>
                            </p>
                        </li>
                        <li class="grid12-4"><span class="no-fuss-payment"></span>                        
                            <p>NO FUSS PAYMENT
                               <label>Many payment options available</label>
                            </p>
                        </li>
                    </ul>
                </div>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Banner Slider 1
     */
    public function updateCmsBlockBannerSlider1() {
        $identifier = 'home_slider_1';
        $title = 'Home : Banner Slider 1';
        $content = '<div class="item">
                        <div class="ban">
                            <a href="category/phonecases">
                                <img src="{{media url=\'wysiwyg/banner/1-desktopnotext.jpg\'}}" class="mobile-hide" alt="Personalized phone cases">
                                <img src="{{media url=\'wysiwyg/banner/us/1-mob.jpg\'}}" class="mobile-show" alt="Personalized phone cases">
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
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Banner Slider 2
     */
    public function updateCmsBlockBannerSlider2() {
        $identifier = 'home_slider_2';
        $title = 'Home : Banner Slider 2';
        $content = '<div class="item">
                        <div class="ban">
                            <a href="category/tabletcases">
                                <img src="{{media url=\'wysiwyg/banner/banner-ipad-desktop.jpg\'}}" class="mobile-hide" alt="Design your own case">
                                <img src="{{media url=\'wysiwyg/banner/us/us-ipad-mobile.jpg\'}}" class="mobile-show" alt="Design your own case">
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
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Banner Slider 3
     */
    public function updateCmsBlockBannerSlider3() {
        $identifier = 'home_slider_3';
        $title = 'Home : Banner Slider 3';
        $content = '<div class="item">
                        <div class="ban">
                            <a href="category/accessories">
                                <img src="{{media url=\'wysiwyg/banner/2-desktopnotext.jpg\'}}" class="mobile-hide" alt="Design your own case">
                                <img src="{{media url=\'wysiwyg/banner/us/2-mob.jpg\'}}" class="mobile-show" alt="Design your own case">
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
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Footer Privacy Links GCUS
     */
    public function updateCmsBlockFooterPrivacyLinksGCUS() {
        $identifier = 'footer_privacy_links';
        $title = 'Footer Privacy Links GCUS';
        $content = '<ul>
                        <li><a href="{{store url="terms-conditions"}}">Terms &amp; Conditions</a></li>
                        <li><a href="{{store url="privacy_policy"}}">Privacy policy</a></li>
                    </ul>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Footer - Info GCUS
     */
    public function updateCmsBlockFooterInfoGCUS() {
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
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Footer - FAQ GCUS
     */
    public function updateCmsBlockFooterFaqGCUS() {
        $identifier = 'block_footer_faq';
        $title = 'Footer - FAQ GCUS';
        $content = '<h4 class="footer-title">HELP</h4>
                    <ul>
                        <li><a href="{{store url="productsoverview"}}">Design dimensions</a></li>
                        <li><a href="{{store url="how-to-create-a-custom-phone-case"}}">How to customize your phone case</a></li>
                        <li><a href="{{store url="help"}}">Frequently asked questions</a></li>
                    </ul>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Footer - MY ACCOUNT GCUS
     */
    public function updateCmsBlockFooterMyaccountGCUS() {
        $identifier = 'footer_block_myaccount';
        $title = 'Footer - MY ACCOUNT GCUS';
        $content = '<h4 class="footer-title">MY ACCOUNT</h4>
                    <ul>
                        <li><a href="{{store url="customer/account/"}}">Account overview </a></li>
                        <li><a href="{{store url="sales/order/history/"}}">Order overview</a></li>
                        <li><a href="{{store url="customer/account/edit/"}}">Change your account details</a></li>
                    </ul>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Footer - Social Links GCUS
     */
    public function updateCmsBlockFooterSocialLinksGCUS() {
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
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Footer Payment Container GCUS
     */
    public function updateCmsBlockFooterPaymentContainerGCUS() {
        $identifier = 'footer_payment_container';
        $title = 'Footer Payment Container GCUS';
        $content = '<div class="footer-payment-container ">
                <div class="container">
                        <div class="half first">
                            <div class="col-sm-12 col-md-12 col-lg-12 clearfix no-padleft mobile-text-hide">
                                <span>Payment Methods</span>
                                <p><img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/EN/payment-US.png" alt=""></p>
                            </div>
                        </div>
                        <div class="half right-text last">
                            <div class="col-sm-12 col-md-12 col-lg-12 text-right clearfix mobile-text-hide">
                                <span>Shipping Partner</span>
                                <p><img src="https://d3813yjxa29jmh.cloudfront.net/media/wysiwyg/EN/shipping-US.png" alt=""></p>
                            </div>
                        </div>
                </div>
            </div>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home page :: Newsletter
     */
    public function updateCmsBlockHomepageNewsletter() {
        $identifier = 'home_newsletter';
        $title = 'Home page :: Newsletter';
        $content = '<div class="block-subscribe">
                        <div class="container">
                            <div class="col-sm-12 col-md-4 col-lg-4  promotional-text">
                                <p>SUBSCRIBE TO OUR NEWSLETTER
                    - 5% OFF YOUR NEXT ORDER!</p>        </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 subscribe-form">
                                <div id="subscribe-form" class="clearer">
                        <form action="https://www.gocustomized.com/newsletter/subscriber/new/" method="post" id="newsletter-validate-detail">
                            <div>
                                <label for="newsletter">Newsletter</label>
                                <div class="input-box">
                                    <input name="email" id="newsletter" placeholder="Enter your e-mail address" title="Sign up for our newsletter" class="input-text required-entry validate-email" type="text">
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
                            <div class="col-sm-12 col-md-4 col-lg-4 social-links">
                                <ul class="newsletter-social-lists">
                        <li class="newsletter-social-list">
                            <a href="https://www.instagram.com/gocustomized/" target="_blank" title="Instgram"><i class="ic-instagram"></i></a>
                        </li>
                        <li class="newsletter-social-list">
                            <a href="https://www.facebook.com/GoCustomized/" target="_blank" title="Facebook"><i class="ic-facebook"></i></a>
                        </li>
                        <li class="newsletter-social-list">
                            <a href="https://twitter.com/gocustomized" target="_blank" title="Twitter"><i class="ic-twitter"></i></a>
                        </li>
                        <li class="newsletter-social-list">
                            <a href="https://www.pinterest.com/gocustomized/" target="_blank" title="Pinterest"><i class="ic-pinterest"></i></a>
                        </li>
                    </ul>        </div>
                        </div>
                    </div>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home Page : Seo Block
     */
    public function updateCmsBlockHomepageSeoBlock() {
        $identifier = 'home_seo_block';
        $title = 'Home Page : Seo Block';
        $content = '<div class="block-graybg seo-block">
                        <div class="block-content  block-padding container">
                        <div class="half first"><h1>Make your own custom phone case</h1><p> Are you looking for original cellphone or tablet cases? Then you’ve come to the right place here at GoCustomized. Almost everyone these days has generic and identical smartphones, so why not make yours unique? Protecting your cellphone is important, but we here at GoCustomized take it a step further by allowing you to create a custom phone case with a picture!</p><br><p>We offer a wide variety of custom phone cases for almost all smartphones. You can make a custom phone case for the iPhone X, iphone 8, 7, 7 Plus, iPhone 6(S) and 6(S) Plus, iPhone SE and 5S, the Samsung Galaxy S6 and S7, S8 edge, the iPad Air and iPad Pro, and additionally HTC, Huawei and Sony phones. You can design your own phone case with a beautiful picture and a heartwarming text, special date or name of a loved one. How you make your customized phone case is entirely up to you! The best part of it all is that you can stand out of the crowd with custom phone cases.</p><h1>iPad cases with pictures</h1><p>In addition to a phone case design, you can make a magnificent case for your tablet! We offer complete unique cases for the best tablets out there, such as the iPad Pro, iPad Air and iPad Mini. For our iPad case lineup, we offer an optional smart case that will protect the back of your device, which can also be customized with your own personal design. In addition to iPad smart covers, hard cases and soft cases, you can design an iPad sleeve! This durable sleeve provides reliable padding and your image is printed on both sides! Personalised tablet covers are becoming very trendy and ensure that your tablet is better protected on the go! So go and make your custom iPad case now!</p></div>

                        <div class="half last"><h1>Large variety of case types</h1><p>We try our best to offer all sorts of custom phone cases and covers. Whether you’re looking for a customized phone case or a custom tablet case, you will be able to find a variety of case types, such as slim cases, soft cases, wallet cases, smart cases, and smart covers. Making a slim case cover will provide your phone with a hard nylon case that protects all corners and is stylized with your very own photo or design. This photo is printed on the back of the case and sometimes even on the sides. A soft case is possible too, if you’re interested in a more rubbery and flexible type of case! High resolution images will be printed on the back of your phone case and are scratch resistant.</p><br><p>Want something more original? Then create your very own wallet case with a design or photo on the front or back of the case. We also offer one of the most innovative and creative custom phone cases on the market in the wooden iPhone case, which can have designs specifically engraved into the back of the smartphone. So when you design your very own custom phone case, be sure to choose the right one that reflects you best! With GoCustomized, creating custom phone cases has never been so easy and fun!</p><br><h1 >Design a case for yourself or for a gift</h1><p>A personalized phone case is also great for a unique and original gift. The picture would give it a very personal touch and it can be uploaded directly from your PC, Instagram or Facebook! Need some inspiration? Then you can make your own with one of our designs. It’s incredibly easy to design your own phone case and can be done quickly!</p></div>
                        </div>
                    </div>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home Page :: About Us
     */
    public function updateCmsBlockHomepageAboutUs() {
        $identifier = 'about_us';
        $title = 'Home Page :: About Us';
        $content = '<div class="block block-about-us padding-bottom-30">
                        <div class="container">
                            <div class="section-title border-bottom">ABOUT US</div>
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
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home page : FIND YOUR DEVICE
     */
    public function updateCmsBlockHomepageFindYourDevice() {
        $identifier = 'find_your_device';
        $title = 'Home page : FIND YOUR DEVICE';
        $content = '<div class="category-section padding-tb-30 container">
                        {{block class="CustomConcepts\UltimoGocustomized\Block\ListFeaturedSlider"
                        template="Infortis_Base::product/list_featured_slider.phtml" category_id="2" product_count="12"
                        hide_button="1" block_name="FIND YOUR DEVICE"}}
                    </div>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home page : BestSeller
     */
    public function updateCmsBlockHomepageBestSeller() {
        $identifier = 'home-page-bestseller';
        $title = 'Home page : Bestseller';
        $content = '<div class="block block-bestseller">
                    <div class="container">
                        {{block class="CustomConcepts\UltimoGocustomized\Block\Bestseller" name="category.bestseller" template="Magento_Catalog::category/category-bestseller.phtml"}}
                    </div>
                </div>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home page : review
     */
    public function updateCmsBlockHomepageReview() {
        $identifier = 'homepage-rating';
        $title = 'Home page : Review';
        $content = '{{block class="CustomConcepts\UltimoGocustomized\Block\HomepageReview" name="kiyohsnippets.review" template="CustomConcepts_UltimoGocustomized::homepage/review.phtml"}}';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home page : B2B mobile
     */
    public function updateCmsBlockB2Bmobile() {
        $identifier = 'block_b2b_toplink';
        $title = 'B2B top link GCES';
        $content = '<li class="b2b" id="link-b2b"><a href="#" title="b2b">b2b</a></li>';
        $this->updateCmsBlock($identifier, $title, $content);
    }

    /**
     * Update a CMS block
     * Home page : Mobile menu bottom about section GCUK
     */
    public function updateCmsBlockAboutsectionMobile() {
        $identifier = 'mobile_menu_about';
        $title = 'Mobile menu bottom about section GCUK';
        $content = '<li id="link-aboutus" class="about"><a title="About us" href="{{store url=""}}about-gocustomized/">About us</a></li>
<li id="link-help" class="help"><a title="Help" href="{{store url=""}}help/">Help</a></li>
<li id="link-contact" class="contact"><a title="Contact" href="{{store url=""}}contact-us/">Contact</a></li>';
        $this->updateCmsBlock($identifier, $title, $content);
    }
    /**
     * Update a CMS block
     * Home page : Mobile menu bottom about section GCUK
     */
    public function updateCmsBlockB2BRequestform() {
        $identifier = 'b2b_requestform_pdp';
        $title = 'B2B request productpage';
        $content = 'Ordering more then 10 units? Get in touch and receive a quote in no time! Complete the form below or send an email to sales@gocustomized.com. Our sales team will process your order request within a few hours and will contact you by phone with further details.
';
        $this->updateCmsBlock($identifier, $title, $content);
    }

}
