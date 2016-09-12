<?php

namespace SaltedHerring;

class Grid {
	public static function make($name, $label = '', $source, $sortable = true, $gridHeaderType = 'GridFieldConfig_RecordEditor') {
		/*
		GridFieldConfig_Base
		GridFieldConfig_RecordViewer
		GridFieldConfig_RecordEditor
		GridFieldConfig_RelationEditor
		*/
		if ($label == '') { $label = $name; }
		$grid = new \GridField($name, $label, $source);
		$config = $gridHeaderType::create();
		$config->removeComponentsByType('GridFieldPaginator')
				->addComponents(
					new \GridFieldPaginatorWithShowAll(30)
				);
		if ($sortable) {
			$config->addComponents(
				$sortable = new \GridFieldOrderableRows('SortOrder')
			);
		}
		
		$grid->setConfig($config);
		return $grid;
	}

	public static function makeEditable($name, $label = '', $source, $sortable = true, $fields = null) {
		$grid = self::make($name, $label, $source, $sortable);
		$config = $grid->getConfig();
		$fieldEditor = new \GridFieldEditableColumns();
		$btnInlineAdd = new \GridFieldAddNewInlineButton();
		
		if (!empty($fields)) {
			$fieldEditor->setDisplayFields($fields);
		}
		
		$config->removeComponentsByType('GridFieldAddNewButton')
				->addComponents(
					$fieldEditor, 
					$btnInlineAdd
				);
		
		return $grid;
	}
}