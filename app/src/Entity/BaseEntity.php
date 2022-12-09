<?php
namespace App\Entity;

use App\Traits\Hydrator;
use JsonSerializable;

abstract class BaseEntity implements JsonSerializable
{
    public function __construct(array $data = [])
    {
       $this->hydrate($data);
    }

    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (is_callable([$this, $method])) {
                $this->$method($value);
            }
        }
    }
}
