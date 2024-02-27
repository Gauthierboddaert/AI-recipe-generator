<?php

namespace App\Service;

use LLPhant\Chat\FunctionInfo\FunctionInfo;

class OpenAiHttp
{
    public function __construct(
        private readonly OpenAiManager $openAiManager,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function request()
    {
        $chat = $this->openAiManager->getOpenAiChat();
        $chat->setSystemMessage('Tu es un expert de la cuisine et je veux que tu me listes les ingrédients pour faire des recettes. Quand tu me listes des ingrédients, je veux que tu me donnes forcément le nombre de gramme, et je ne veux pas de environ, je veux un poids fixe');
        $response = $chat->generateText('voici une liste d ingrédient sépraré par un ;,je veux que tu me créer une recette en me listant uniquement les ingrédients et en listant le nombre de gramme, fait moi une recette avec: tomate; oignon; ail; poivron; huile; sel; poivre; eau;');

        dd($response);

    }
}