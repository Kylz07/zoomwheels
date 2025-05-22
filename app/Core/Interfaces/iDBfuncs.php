<?php
declare(strict_types=1);


interface iDBFuncs {
	  public function table($tablename): object;
	  public function insert(Array $values): int;
	  public function get(): array; 
	  public function getAll(): array;
	  public function select(Array $fieldList=null): object;
	  public function from($table): object;
	  public function where(): object;
	  public function whereOr(): object;
	  public function showQuery(): string;
	  public function update(Array $values): int;
      public function delete(): int;
	  public function showValueBag(): array;
}