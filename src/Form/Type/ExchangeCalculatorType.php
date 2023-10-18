<?php

namespace App\Form\Type;

use App\Repository\ExchangeRateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ExchangeCalculatorType extends AbstractType
{
    public const FROM_FIELD = 'from';
    public const TO_FIELD = 'to';
    public const AMOUNT_FIELD = 'amount';

    public function __construct(
        private readonly ExchangeRateRepository $exchangeRateRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currencies = $this->exchangeRateRepository->getAvailableExchangeCurrencies();

        $choices = array_combine($currencies, $currencies);

        $builder
            ->add(
                self::FROM_FIELD,
                ChoiceType::class,
                ['choices' => $choices, 'required' => true]
            )
            ->add(
                self::TO_FIELD,
                ChoiceType::class,
                ['choices' => $choices, 'required' => true]
            )
            ->add(
                self::AMOUNT_FIELD,
                NumberType::class,
                ['required' => true]
            )
            ->add(
                'submit',
                SubmitType::class
            );
    }
}