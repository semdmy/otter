<?php

declare(strict_types=1);

namespace Ghost\Setup\Setup;

use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Symfony\Component\Console\Output\StreamOutput;
use Magento\Framework\Filesystem\Driver\File;

class FileManager
{
    protected StreamOutput $output;

    /**
     * @throws FileSystemException
     */
    public function __construct(
        protected File           $file,
        protected FixtureManager $fixtureManager,
        protected Csv            $csv
    )
    {
        $this->output = new StreamOutput($file->fileOpen('php://stdout', 'w'));
    }

    /**
     * @throws LocalizedException
     * @throws Exception
     */
    public function getParsedFixtureData(string $fixture): array
    {
        $rows = $this->csv->getData($this->fixtureManager->getFixture($fixture));
        $rowsHeader = array_shift($rows);
        foreach ($rows as &$row) {
            $row = array_combine($rowsHeader, $row);
        }
        return $rows;
    }

    /**
     * Get output stream
     *
     * @return StreamOutput
     */
    public function getOutputStream(): StreamOutput
    {
        return $this->output;
    }
}
