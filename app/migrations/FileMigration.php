<?php namespace app\migrations;

use spitfire\storage\database\drivers\SchemaMigrationExecutorInterface;
use spitfire\storage\database\drivers\TableMigrationExecutorInterface;
use spitfire\storage\database\MigrationOperationInterface;

class FileMigration implements MigrationOperationInterface
{
	
	public function identifier(): string
	{
		return 'files.create';
	}
	
	public function description(): string
	{
		return 'Adds the files, apps and uploads table';
	}
	
	public function up(SchemaMigrationExecutorInterface $schema): void
	{
		$schema->add('apps', function (TableMigrationExecutorInterface $table) {
			$table->id();
			$table->string('secret', 255, false); # For symmetrical signatures
			$table->string('publickey', 255, true); # For assymmetrical signatures
			$table->timestamps();
			$table->softDelete();
		});
		
		$schema->add('files', function (TableMigrationExecutorInterface $table) {
			$table->id();
			$table->string('filename', 255, false);
			$table->string('poster', 255, false);
			$table->string('lqip', 1024, false);
			$table->string('contentType', 255, false);
			$table->int('animated', true, false); # Indicates whether the content is encoded as video
			$table->int('length', true, false);
			$table->string('md5', 255, false);
			$table->timestamps();
			
			$table->index('hashidx', ['md5', 'length']);
		});
		
		$schema->add('uploads', function (TableMigrationExecutorInterface $table) use ($schema) {
			$table->id();
			$table->foreign('file', $schema->table('files'));
			$table->foreign('app', $schema->table('apps'));
			$table->string('secret', 255, false);
			$table->timestamps();
			$table->softDelete();
		});
	}
	
	public function down(SchemaMigrationExecutorInterface $schema): void
	{
		$schema->drop('uploads');
		$schema->drop('files');
		$schema->drop('apps');
	}
}
