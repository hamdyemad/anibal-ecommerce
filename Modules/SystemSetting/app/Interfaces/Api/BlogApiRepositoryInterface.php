<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface BlogApiRepositoryInterface
{
    public function all($filters = []);

    public function find($id);

    public function getHostTopics($filters = []);

    public function addComment($blog, $data);
}
