<?php

namespace ErikFig\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

class PokeApi extends Command
{
    protected function configure()
    {
        $this->setName('poke:who');

        $this->addArgument('name', InputArgument::REQUIRED, 'O nome ou ID do Pokemon');
        $this->addArgument('attribute', InputArgument::OPTIONAL, 'Retorna um atributo específico');
        $this->addArgument('attributes', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Retorna vários atributos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Quem é esse pokemon?',
            '======'
        ]);

        $output->writeln('Aguarde, estamos nos conectando a API');

        $client = new \GuzzleHttp\Client;
        $response = $client->request('GET', 'http://pokeapi.co/api/v2/pokemon/' . $input->getArgument('name'));

        $pokeData = (string)$response->getBody();
        $pokeData = json_decode($pokeData);
        $abilities = $pokeData->abilities;

        $pokeData = [
            'name' => 'name:' . $pokeData->name,
            'base_experience' => 'base_experience:' . $pokeData->base_experience,
            'height' => 'height:' . $pokeData->height,
            'order' => 'order:' . $pokeData->order,
            'weight' => 'weight:' . $pokeData->weight
        ];

        $toOutput = [];

        if ($input->getArgument('attribute')) {
            $toOutput[] = $pokeData[$input->getArgument('attribute')] ?? 'não encontrei este atributo';
        }

        foreach ($input->getArgument('attributes') as $attribute) {
            $toOutput[] = $pokeData[$attribute] ?? 'não encontrei este atributo';
        }

        if ($toOutput === []) {
            $toOutput = $pokeData;
        }

        if ($input->getOption('abilities')) {
            $abilitiesToOutput = [];
            foreach ($abilities as $ability) {
                $abilitiesToOutput[] = 'ability: '. $ability->ability->name;
            }
            $toOutput = array_merge($toOutput, $abilitiesToOutput);
        }

        $output->write('Finalizamos o ');
        $output->write('processo');
    }
}
