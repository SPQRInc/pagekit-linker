<?php

return [
	
	/*
	 * Installation hook
	 *
	 */
	'install'   => function( $app ) {
		$util = $app[ 'db' ]->getUtility();
		if ( $util->tableExists( '@linker_target' ) === false ) {
			$util->createTable(
				'@linker_target',
				function( $table ) {
					$table->addColumn(
						'id',
						'integer',
						[
							'unsigned'      => true,
							'length'        => 10,
							'autoincrement' => true
						]
					);
					$table->addColumn( 'title', 'string', [ 'length' => 255 ] );
					$table->addColumn( 'slug', 'string', [ 'length' => 255 ] );
					$table->addColumn( 'status', 'smallint' );
					$table->addColumn( 'target_url', 'string', [ 'length' => 255 ] );
					$table->addColumn(
						'clickcount',
						'integer',
						[
							'unsigned' => true,
							'length'   => 10,
							'default'  => 0,
							'notnull'  => false
						]
					);
					$table->addColumn(
						'viewcount',
						'integer',
						[
							'unsigned' => true,
							'length'   => 10,
							'default'  => 0,
							'notnull'  => false
						]
					);
					$table->addColumn( 'data', 'json_array', [ 'notnull' => false ] );
					$table->addColumn( 'date', 'datetime', [ 'notnull' => false ] );
					$table->addColumn( 'modified', 'datetime' );
					$table->setPrimaryKey( [ 'id' ] );
					$table->addUniqueIndex( [ 'slug' ], '@LINKER_SLUG' );
				}
			);
		}
		
		if ( $util->tableExists( '@linker_marker' ) === false ) {
			$util->createTable(
				'@linker_marker',
				function( $table ) {
					$table->addColumn(
						'id',
						'integer',
						[
							'unsigned'      => true,
							'length'        => 10,
							'autoincrement' => true
						]
					);
					$table->addColumn( 'target_id', 'integer', [ 'unsigned' => true, 'length' => 10, 'default' => 0 ] );
					$table->addColumn( 'type', 'string', [ 'length' => 255 ] );
					$table->addColumn( 'value', 'string', [ 'length' => 255 ] );
					$table->setPrimaryKey( [ 'id' ] );
				}
			);
		}
		
		if ( $util->tableExists( '@linker_statistic' ) === false ) {
			$util->createTable(
				'@linker_statistic',
				function( $table ) {
					$table->addColumn(
						'id',
						'integer',
						[
							'unsigned'      => true,
							'length'        => 10,
							'autoincrement' => true
						]
					);
					$table->addColumn( 'marker_id', 'integer', [ 'unsigned' => true, 'length' => 10, 'default' => 0 ] );
					$table->addColumn( 'type', 'string', [ 'length' => 255 ] );
					$table->addColumn( 'ip', 'string', [ 'length' => 255, 'notnull' => false ] );
					$table->addColumn( 'referrer', 'string', [ 'length' => 255, 'notnull' => false ] );
					$table->addColumn( 'date', 'datetime', [ 'notnull' => false ] );
					$table->setPrimaryKey( [ 'id' ] );
				}
			);
		}
	},
	
	/*
	 * Enable hook
	 *
	 */
	'enable'    => function( $app ) {
	},
	
	/*
	 * Uninstall hook
	 *
	 */
	'uninstall' => function( $app ) {
		// remove the tables
		$util = $app[ 'db' ]->getUtility();
		if ( $util->tableExists( '@linker_target' ) ) {
			$util->dropTable( '@linker_target' );
		}
		if ( $util->tableExists( '@linker_marker' ) ) {
			$util->dropTable( '@linker_marker' );
		}
		if ( $util->tableExists( '@linker_statistic' ) ) {
			$util->dropTable( '@linker_statistic' );
		}
		
		// remove the config
		$app[ 'config' ]->remove( 'spqr/linker' );
	},
	
	/*
	 * Runs all updates that are newer than the current version.
	 *
	 */
	'updates'   => [],

];