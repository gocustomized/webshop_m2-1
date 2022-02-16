<?php
namespace CustomConcepts\Faqs\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_LIST_PAGE_TITLE				    =	'prodfaqs/list/page_title';
    const XML_PATH_LIST_IDENTIFIER				    =	'prodfaqs/list/identifier';
    const XML_PATH_LIST_META_DESCRIPTION	        =	'prodfaqs/list/meta_description';
    const XML_PATH_LIST_META_KEYWORDS			    =	'prodfaqs/list/meta_keywords';
    const XML_PATH_LIST_SORTING				        =	'prodfaqs/list/sort_by';
    const XML_PATH_LIST_LIKE				        =	'prodfaqs/list/like';

    const XML_PRODUCT_PAGE_FAQS_ENABLE			    =	'prodfaqs/product_page/enable';
    const XML_PRODUCT_PAGE_TITLE				    =	'prodfaqs/product_page/title';
    const XML_PRODUCT_FAQ_RATING_ENABLE			    =	'prodfaqs/product_page/enable_rating';
    const XML_PRODUCT_FAQ_RATING_CUSTOMERS		    =	'prodfaqs/product_page/allow_customers';
    const XML_PRODUCT_FAQ_ACCORDION				    =	'prodfaqs/product_page/enable_accordion';
    const XML_PRODUCT_FAQ_SORTING				    =	'prodfaqs/product_page/sort_by';
    const XML_PRODUCT_FAQ_LIKE				        =	'prodfaqs/product_page/like';

    const XML_PRODUCT_ASK_ENABLE				    =	'prodfaqs/product_ask/enable';
    const XML_PRODUCT_OPEN_FORM				        =	'prodfaqs/product_ask/open_form';

    const XML_MODERATOR_EMAIL_SUBJECT			    =	'prodfaqs/email_settings/moderator_email_subject';
    const XML_MODERATOR_EMAIL_ID				    =	'prodfaqs/email_settings/moderator_email';
    const XML_MODERATOR_EMAIL_TEMPLATE			    =	'prodfaqs/email_settings/moderator_email_template';
    const XML_SENDER_EMAIL					        =	'prodfaqs/email_settings/email_sender';
    const XML_CLIENT_EMAIL_SUBJECT				    =	'prodfaqs/email_settings/client_email_subject';
    const XML_CLIENT_EMAIL_TEMPLATE				    =	'prodfaqs/email_settings/client_email_template';

    const XML_PATH_DETAIL_TITLE_PREFIX			    =	'prodfaqs/detail/title_prefix';
    const XML_PATH_DETAIL_DEFAULT_META_DESCRIPTION	=	'prodfaqs/detail/default_meta_description';
    const XML_PATH_DETAIL_DEFAULT_META_KEYWORDS		=	'prodfaqs/detail/default_meta_keywords';
    const XML_PATH_SEO_URL_SUFFIX				    =	'prodfaqs/seo/url_suffix';

    const XML_SHOW_NUMBER_OF_QUESTIONS              = 'prodfaqs/list/show_number_of_questions';
    const XML_DISPLAY_CATEGORIES                    = 'prodfaqs/list/display_categories';
    const XML_SHOW_SHIPPING_ON_ID                   = 'prodfaqs/general/show_shipping_on_id';
    const XML_FAQ_MAX_TOPIC                         = 'prodfaqs/general/faq_maxtopic';
    const XML_FAQ_BLOCK                             = 'prodfaqs/general/faq_block';
    const XML_FAQ_BLOCK_VIEW_BY                     = 'prodfaqs/general/faq_block_view_by';

    /**
     * @return mixed|string
     */
    public function getListIdentifier(){
        $identifier = $this->scopeConfig->getValue(self::XML_PATH_LIST_IDENTIFIER);
        if (!$identifier) {
            $identifier = 'faqs';
        }
        return $identifier;
    }

    /**
     * @return mixed
     */
    public function getSeoUrlSuffix(){
        return $this->scopeConfig->getValue(self::XML_PATH_SEO_URL_SUFFIX);
    }

    /**
     * @return mixed
     */
    public function getGeneralFaqSorting()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LIST_SORTING);
    }

    /**
     * @return mixed
     */
    public function getShowNumberOfQuestions(){
        return $this->scopeConfig->getValue(self::XML_SHOW_NUMBER_OF_QUESTIONS);
    }

    /**
     * @return mixed
     */
    public function getDisplayCategories(){
        return $this->scopeConfig->getValue(self::XML_DISPLAY_CATEGORIES);
    }

    /**
     * @return mixed
     */
    public function showShippingOnId(){
        return $this->scopeConfig->getValue(self::XML_SHOW_SHIPPING_ON_ID);
    }

    /**
     * @return mixed
     */
    public function getFaqMaxTopic(){
        return $this->scopeConfig->getValue(self::XML_FAQ_MAX_TOPIC);
    }

    /**
     * @return mixed
     */
    public function getFaqBlock(){
        return $this->scopeConfig->getValue(self::XML_FAQ_BLOCK);
    }

    public function getFaqBlockViewBy(){
        return $this->scopeConfig->getValue(self::XML_FAQ_BLOCK_VIEW_BY);
    }
}
