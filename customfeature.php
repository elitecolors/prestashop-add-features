<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Entity\Feature;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShopBundle\Form\Admin\Feature\ProductFeature;
use Symfony\Component\HttpFoundation\Response;

class customfeature extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'customfeature';
        $this->author = 'Saidani Ahmed';
        $this->author_uri = 'saidaniahmed125@gmail.com';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Add all Featured to products', [], 'Modules.Customfeature.Admin');
        $this->description = $this->trans('Product page admin button to add all features.', [], 'Modules.Customfeature.Admin');

        $this->templateFile = 'module:customfeature/views/templates/hook/customfeature.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

        return parent::install()
            && $this->registerHook('displayAdminProductsMainStepLeftColumnBottom')
            && $this->registerHook('actionAdminControllerSetMedia')
            ;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    public function hookActionAdminControllerSetMedia()
    {
        if ('AdminProducts' == $this->context->controller->controller_name) {
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }

            $this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
        }
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('customfeature'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('customfeature'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $features = Feature::getFeatures($this->context->language->getId());

        if (!empty($features)) {
            return [
                'features' => $this->getHTMLFormFeatures($features),
            ];
        }

        return false;
    }

    private function getHTMLFormFeatures(array $features)
    {
        $allFeaturesHtml = '';
        foreach ($features as $featureData) {
            $featuresValues = FeatureValue::getFeatureValuesWithLang($this->context->language->getId(), $featureData['id_feature']);

            if (empty($featuresValues)) {
                return;
            }

            foreach ($featuresValues as $value) {
                $itemForm = [
                    'feature' => $featureData['id_feature'],
                    'value' => $value['id_feature_value'],
                    'custom_value' => null,
                ];

                $configurationForm = $this->createForm(ProductFeature::class, $itemForm);

                $formContent = $this->getContentForm('@Product/ProductPage/Forms/form_feature.html.twig', [
                    'form' => $configurationForm->createView(),
                ]);

                $allFeaturesHtml .= $formContent->getContent();
            }
        }

        return $allFeaturesHtml;
    }

    protected function getContentForm(string $view, array $parameters = [], Response $response = null): Response
    {
        $content = $this
            ->getContainer()
            ->get('twig')
            ->render($view, $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    private function createForm(string $type, $data, $options = [])
    {
        return $this
           ->getContainer()
           ->get('form.factory')
           ->create($type, $data, $options);
    }
}
