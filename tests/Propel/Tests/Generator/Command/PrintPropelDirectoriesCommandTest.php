<?php

/**
 * MIT License. This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Tests\Generator\Command;

use Propel\Generator\Application;
use Propel\Generator\Command\PrintPropelDirectoriesCommand;
use Propel\Runtime\Propel;
use Propel\Tests\TestCaseFixtures;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class PrintPropelDirectoriesCommandTest extends TestCaseFixtures
{
    /**
     * @return void
     */
    public function testPrintNamespacedFixtures(): void
    {
        if (!class_exists('\Symfony\Component\Filesystem\Path')) {
            $this->markTestSkipped("('\Symfony\Component\Console\Path not available");
        }

        $firstLine = extension_loaded('xdebug') ? "You are running perpl with xdebug enabled. This has a major impact on runtime performance.\n\n" : '';
        $rootDir = realpath(__DIR__ . '/../../../../../');

        $this->assertCommandOutput(
            [
                '--schema-dir' => "$rootDir/tests/Fixtures/namespaced",
                '--config-dir' => "$rootDir/tests/Fixtures/namespaced",
            ],
            "{$firstLine}Directory structure and files according to current config (directories marked as relative change when perpl is called from a different path):

└── $rootDir/
     ├── generated-classes/
     │    │  paths.phpDir Base target directory for model:build !from relative path!
     │    ├── Baz/
     │    │    ├── Base/
     │    │    │    ├── Collection/
     │    │    │    │    ├─╼ NamespacedBookClubCollection.php (new)
     │    │    │    │    ├─╼ NamespacedBookListRelCollection.php (new)
     │    │    │    │    └─╼ NamespacedPublisherCollection.php (new)
     │    │    │    ├─╼ NamespacedBookClub.php (new)
     │    │    │    ├─╼ NamespacedBookClubQuery.php (new)
     │    │    │    ├─╼ NamespacedBookListRel.php (new)
     │    │    │    ├─╼ NamespacedBookListRelQuery.php (new)
     │    │    │    ├─╼ NamespacedPublisher.php (new)
     │    │    │    └─╼ NamespacedPublisherQuery.php (new)
     │    │    ├── Map/
     │    │    │    ├─╼ NamespacedBookClubTableMap.php (new)
     │    │    │    ├─╼ NamespacedBookListRelTableMap.php (new)
     │    │    │    └─╼ NamespacedPublisherTableMap.php (new)
     │    │    ├─╼ NamespacedBookClub.php (new)
     │    │    ├─╼ NamespacedBookClubQuery.php (new)
     │    │    ├─╼ NamespacedBookListRel.php (new)
     │    │    ├─╼ NamespacedBookListRelQuery.php (new)
     │    │    ├─╼ NamespacedPublisher.php (new)
     │    │    └─╼ NamespacedPublisherQuery.php (new)
     │    └── Foo/Bar/
     │         ├── Base/
     │         │    ├── Collection/
     │         │    │    ├─╼ NamespacedAuthorCollection.php (new)
     │         │    │    ├─╼ NamespacedBookCollection.php (new)
     │         │    │    └─╼ NamespacedBookstoreEmployeeCollection.php (new)
     │         │    ├─╼ NamespacedAuthor.php (new)
     │         │    ├─╼ NamespacedAuthorQuery.php (new)
     │         │    ├─╼ NamespacedBook.php (new)
     │         │    ├─╼ NamespacedBookQuery.php (new)
     │         │    ├─╼ NamespacedBookstoreCashierQuery.php (new)
     │         │    ├─╼ NamespacedBookstoreEmployee.php (new)
     │         │    ├─╼ NamespacedBookstoreEmployeeQuery.php (new)
     │         │    └─╼ NamespacedBookstoreManagerQuery.php (new)
     │         ├── Map/
     │         │    ├─╼ NamespacedAuthorTableMap.php (new)
     │         │    ├─╼ NamespacedBookTableMap.php (new)
     │         │    └─╼ NamespacedBookstoreEmployeeTableMap.php (new)
     │         ├─╼ NamespacedAuthor.php (new)
     │         ├─╼ NamespacedAuthorQuery.php (new)
     │         ├─╼ NamespacedBook.php (new)
     │         ├─╼ NamespacedBookQuery.php (new)
     │         ├─╼ NamespacedBookstoreCashier.php (new)
     │         ├─╼ NamespacedBookstoreCashierQuery.php (new)
     │         ├─╼ NamespacedBookstoreEmployee.php (new)
     │         ├─╼ NamespacedBookstoreEmployeeQuery.php (new)
     │         ├─╼ NamespacedBookstoreManager.php (new)
     │         └─╼ NamespacedBookstoreManagerQuery.php (new)
     ├── generated-conf/
     │    │  paths.phpConfDir Perpl configurations files (from config:convert and model:build). !from relative path!
     │    └─╼ loadDatabase.php (new)
     ├── generated-migrations/
     │       paths.migrationDir Migration files (target for migration:create, migration:migrate, etc) !from relative path!
     ├── generated-sql/
     │       paths.sqlDir SQL database initialization files for sql:insert (user-generated and generated from schema.xml by sql:build) !from relative path!
     └── tests/Fixtures/namespaced/
          │  paths.schemaDir Schema XML files (input for migration:diff, database:reverse, etc)
          └─╼ schema.xml 
"
        );
    }

    /**
     * @return void
     */
    protected function assertCommandOutput(array $input, string $expectedOutput): void
    {
        $app = new Application('Propel', Propel::VERSION);
        $command = new PrintPropelDirectoriesCommand();
        $app->add($command);

        $input = new ArrayInput(['command' => 'config:preview', ...$input]);
        $output = new StreamOutput(fopen('php://temp', 'r+'));

        $app->setAutoExit(false);
        $exitCode = $app->run($input, $output);

        $stream = $output->getStream();
        $result = rewind($stream) ? stream_get_contents($stream) : 'no output';

        $this->assertEquals(0, $exitCode, "Command config:preview should exit successfully, but got output:\n\n " . $result);
        $this->assertEquals($expectedOutput, $result);
    }
}
