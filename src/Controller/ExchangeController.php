<?php

namespace App\Controller;

use App\Form\Type\ExchangeCalculatorType;
use App\Model\ExchangeDTO;
use App\Stonks\Service\Calculator;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeController extends AbstractController
{
    #[Route('/')]
    #[Template('base.html.twig')]
    public function test(Calculator $calculator, Request $request): Response
    {
        $dto = new ExchangeDTO;

        $form = $this->createForm(ExchangeCalculatorType::class, $dto);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ExchangeDTO $dto */
            $dto = $form->getData();

            $convertedAmount = $calculator->exchange($dto->from, $dto->to, $dto->amount);

            $result = sprintf("%f %s = %f %s", $dto->amount, $dto->from, $convertedAmount, $dto->to);
        }

        return $this->render('calc.html.twig', compact('form', 'result'));
    }
}