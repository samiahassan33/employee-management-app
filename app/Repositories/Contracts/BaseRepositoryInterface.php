<?php 

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']);
    public function find($id, array $columns = ['*']);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15, array $columns = ['*']);
    public function findWhere(array $criteria, array $columns = ['*']);
}

