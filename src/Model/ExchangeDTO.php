<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeDTO
{
        #[Assert\NotBlank]
        #[Assert\Length(3)]
        public string $from;

        #[Assert\NotBlank]
        #[Assert\Length(3)]
        public string $to;

        #[Assert\Positive]
        public float $amount;
}