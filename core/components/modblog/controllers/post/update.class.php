<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/'.$modx->getOption('manager_theme',null,'default').'/resource/update.class.php';
/**
 * @package modblog
 */
class BlogPostUpdateManagerController extends ResourceUpdateManagerController {

    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $blogAssetsUrl = $this->modx->getOption('modblog.assets_url',$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/modblog/');
        $quipAssetsUrl = $this->modx->getOption('quip.assets_url',$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/quip/');
        $connectorUrl = $blogAssetsUrl.'connector.php';
        $blogJsUrl = $blogAssetsUrl.'js/';
        $this->addJavascript($managerUrl.'assets/modext/util/datetime.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.grid.resource.security.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($managerUrl.'assets/modext/sections/resource/update.js');
        $this->addJavascript($blogJsUrl.'modblog.js');

        $this->addCss($quipAssetsUrl.'css/mgr.css');
        $this->addJavascript($quipAssetsUrl.'js/quip.js');
        $this->addJavascript($quipAssetsUrl.'js/widgets/comments.grid.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Quip.config = '.$this->modx->toJSON(array()).';
            Quip.config.connector_url = "'.$quipAssetsUrl.'connector.php";
            Quip.request = '.$this->modx->toJSON($_GET).';
        });
        </script>');
        $this->addLastJavascript($blogJsUrl.'post/update.js');
        $this->addHtml('
        <script type="text/javascript">
        // <![CDATA[
        modBlog.connector_url = "'.$connectorUrl.'";
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->resource->get('context_key').'";
        Ext.onReady(function() {
            MODx.load({
                xtype: "modblog-page-blog-post-update"
                ,resource: "'.$this->resource->get('id').'"
                ,record: '.$this->modx->toJSON($this->resourceArray).'
                ,publish_document: "'.$this->canPublish.'"
                ,preview_url: "'.$this->previewUrl.'"
                ,locked: '.($this->locked ? 1 : 0).'
                ,lockedText: "'.$this->lockedText.'"
                ,canSave: '.($this->canSave ? 1 : 0).'
                ,canEdit: '.($this->canEdit ? 1 : 0).'
                ,canCreate: '.($this->canCreate ? 1 : 0).'
                ,canDuplicate: '.($this->canDuplicate ? 1 : 0).'
                ,canDelete: '.($this->canDelete ? 1 : 0).'
                ,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
                ,mode: "update"
            });
        });
        // ]]>
        </script>');
        /* load RTE */
        $this->loadRichTextEditor();
    }
    public function getLanguageTopics() {
        return array('resource','modblog:default','quip:default');
    }


    public function process(array $scriptProperties = array()) {
        $placeholders = parent::process($scriptProperties);
        $this->resourceArray['richtext'] = 1;
        $this->getTagsTV();

        return $placeholders;
    }

    public function getTagsTV() {
        /** @var modTemplateVar $tv */
        $tv = $this->modx->getObject('modTemplateVar',array(
            'name' => 'modblogtags',
        ));
        if ($tv) {
            $this->resourceArray['tags'] = $this->resource->getTVValue('modblogtags');
            $this->resourceArray['tagsId'] = $tv->get('id');
        }
        return $tv;
    }
}