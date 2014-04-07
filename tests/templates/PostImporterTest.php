<?php
class PostImporterTest extends WP_UnitTestCase{

	var $importer;

	public function setUp(){
        parent::setUp();
        $this->importer = $GLOBALS['jcimporter'];
    }

    /**
     * @group core
     * @group template
     */
    public function testCSVPostImporter(){

        $post_id = create_csv_importer(null, 'post', $this->importer->plugin_dir . '/tests/data/data-posts.csv', array(
            'post' => array(
            	'post_title' => '{0}',
				'post_name' => '{1}',
				'post_excerpt' => '{3}',
				'post_content' => '{2}',
				// 'post_author' => '',
				'post_status' => '{4}',
				// 'post_date' => '',
            )
        ));

        // setup taxonomies
        ImporterModel::setImporterMeta($post_id, array('_taxonomies', 'post'), array(
            'tax' => array( 'post_tag'),
            'term' => array('{6}'),
            'permissions' => array('create')
        ));

        ImporterModel::setImporterMeta($post_id, array('_template_settings','enable_post_status'), 1);

    	/**
    	 * Test: Check if one record is returned
    	 */
    	ImporterModel::clearImportSettings();
        $this->importer->importer = new JC_Importer_Core($post_id);
        $import_data = $this->importer->importer->run_import(1);
    	$this->assertEquals(1, count($import_data));

        $import_data = array_shift($import_data);
        // $this->assertEquals('title', $import_data['post']['post_title']);
        $this->assertEquals('slug', $import_data['post']['post_name']);
        $this->assertEquals('excerpt', $import_data['post']['post_excerpt']);
        $this->assertEquals('content', $import_data['post']['post_content']);
        $this->assertEquals('status', $import_data['post']['post_status']);
        $this->assertEquals('I', $import_data['post']['_jci_type']);
        $this->assertEquals('S', $import_data['post']['_jci_status']);
        $this->assertEquals('S', $import_data['_jci_status']);

        //  Test Taxonomies
        $this->assertEquals('tags', $import_data['taxonomies']['post_tag'][0]);
    }

    /**
     * @group core
     * @group template
     */
    public function testXMLPostImporter(){

        $post_id = create_xml_importer(null, 'post', $this->importer->plugin_dir . '/tests/data/data-posts.xml', array(
            'post' => array(
                'base' => '',
                'post_title' => '{/title}',
                'post_name' => '{/slug}',
                'post_excerpt' => '{/excerpt}',
                'post_content' => '{/content}',
                // 'post_author' => '',
                'post_status' => '{/status}',
                // 'post_date' => '',
            )
        ), array(
            'import_base' => '/posts/post',
            'group_base' => array(
                 'post' => array('base' => '')
            )
        ));  

        // setup taxonomies
        ImporterModel::setImporterMeta($post_id, array('_taxonomies', 'post'), array(
            'tax' => array( 'category', 'post_tag'),
            'term' => array('{/categories[1]/category}', '{/tags[1]}'),
            'permissions' => array('overwrite', 'overwrite')
        ));

        ImporterModel::setImporterMeta($post_id, array('_template_settings','enable_post_status'), 1);

        /**
         * Test: Check if one record is returned
         */
        ImporterModel::clearImportSettings();
        $this->importer->importer = new JC_Importer_Core($post_id);
        $import_data = $this->importer->importer->run_import(1);
        $this->assertEquals(1, count($import_data));
        $import_data = array_shift($import_data);

        // assetions
        $this->assertEquals('Post One', $import_data['post']['post_title']);
        $this->assertEquals('post-one-123', $import_data['post']['post_name']);
        $this->assertEquals('This is the post one\'s excerpt', $import_data['post']['post_excerpt']);
        $this->assertEquals('This is the post one\'s content', $import_data['post']['post_content']);
        $this->assertEquals('publish', $import_data['post']['post_status']);
        $this->assertEquals('I', $import_data['post']['_jci_type']);
        $this->assertEquals('S', $import_data['post']['_jci_status']);

        // need to fix failing test, but not failing on real import
        $this->assertEquals('S', $import_data['_jci_status']);
        
    }
}
?>