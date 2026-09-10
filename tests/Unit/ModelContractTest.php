<?php

namespace Tests\Unit;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Tests\TestCase;

class ModelContractTest extends TestCase
{
    #[DataProvider('modelProvider')]
    public function test_every_model_has_a_valid_eloquent_identity(string $className): void
    {
        $this->assertTrue(is_a($className, Model::class, true));

        /** @var Model $model */
        $model = new $className();

        $this->assertNotSame('', trim($model->getTable()), "{$className} must define a table name.");
        $this->assertNotSame('', trim($model->getKeyName()), "{$className} must define a primary key.");
        $this->assertIsArray($model->getCasts());
    }

    #[DataProvider('typedRelationshipProvider')]
    public function test_every_typed_model_relationship_returns_an_eloquent_relation(
        string $className,
        string $methodName
    ): void {
        /** @var Model $model */
        $model = new $className();
        $relationship = $model->{$methodName}();

        $this->assertInstanceOf(
            Relation::class,
            $relationship,
            "{$className}::{$methodName}() must return an Eloquent relationship."
        );
    }

    public function test_legacy_untyped_model_relationships_return_eloquent_relations(): void
    {
        $relationships = [
            [new Module(), 'lessons'],
            [new Lesson(), 'module'],
            [new Lesson(), 'users'],
        ];

        foreach ($relationships as [$model, $methodName]) {
            $this->assertInstanceOf(Relation::class, $model->{$methodName}());
        }
    }

    /** @return iterable<string, array{class-string<Model>}> */
    public static function modelProvider(): iterable
    {
        foreach (self::modelClasses() as $className) {
            yield $className => [$className];
        }
    }

    /** @return iterable<string, array{class-string<Model>, string}> */
    public static function typedRelationshipProvider(): iterable
    {
        foreach (self::modelClasses() as $className) {
            $reflection = new ReflectionClass($className);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getDeclaringClass()->getName() !== $className
                    || $method->getNumberOfRequiredParameters() > 0) {
                    continue;
                }

                $returnType = $method->getReturnType();
                if (! $returnType instanceof ReflectionNamedType || $returnType->isBuiltin()) {
                    continue;
                }

                $returnClass = $returnType->getName();
                if (! class_exists($returnClass) || ! is_a($returnClass, Relation::class, true)) {
                    continue;
                }

                yield "{$className}::{$method->getName()}" => [$className, $method->getName()];
            }
        }
    }

    /** @return array<int, class-string<Model>> */
    private static function modelClasses(): array
    {
        $root = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app';
        $modelRoot = $root.DIRECTORY_SEPARATOR.'Models';
        $files = glob($modelRoot.DIRECTORY_SEPARATOR.'*.php') ?: [];
        $classes = [];

        foreach ($files as $file) {
            $classes[] = 'App\\Models\\'.pathinfo($file, PATHINFO_FILENAME);
        }

        sort($classes);

        return $classes;
    }
}
