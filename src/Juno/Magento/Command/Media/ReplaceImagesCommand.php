<?php


namespace Juno\Magento\Command\Media;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class ReplaceImagesCommand extends AbstractMagentoCommand
{
    protected $allowedTypes = array();

    protected function configure()
    {
        $this->allowedTypes = array('jpg', 'jpeg', 'gif', 'png');

        $this->setName('media:replace-images')
            ->addArgument('imgDir', InputArgument::REQUIRED, 'Source directory')
            ->addArgument('targetDir', InputArgument::REQUIRED, 'Target directory')
            ->setDescription('Copy add/or replace images from source directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->files()
            ->followLinks(true)
            ->in($input->getArgument('imgDir'));

        foreach ($finder as $file) {
            $filename = $file->getFilename();
            $first = strtolower($filename[0]);
            $second = strtolower($filename[1]);

            $type = $file->getExtension();
            if (!in_array($type, $this->allowedTypes)) {
                $output->writeln('<info>Incorrect filetype </info>');
                continue;
            }

            $targetPath = $input->getArgument('targetDir') . DIRECTORY_SEPARATOR . $first . DIRECTORY_SEPARATOR . $second . DIRECTORY_SEPARATOR;

            mkdir($targetPath, 0775, true);
            copy($file->getPathname(), $targetPath.$file->getFilename());

            $output->writeln('<info>'. $filename .' copied to ' . $input->getArgument('targetDir') . '</info>');
        }
    }
}
