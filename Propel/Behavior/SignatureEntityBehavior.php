<?php

namespace Documatic\Bundle\DocumaticBundle\Propel\Behavior;

use Behavior;
use ForeignKey;

class SignatureEntityBehavior extends Behavior
{
    public function objectMethods($builder)
    {
        $builder->declareClass('Documatic\Bundle\DocumaticBundle\Model\SignatureEntityInterface');

        $builder->declareClass('Documatic\Bundle\DocumaticBundle\Model\SignatureEntityTrait');
    }

    public function objectFilter(&$script)
    {
        $pattern = '/class (\w+) extends (\w+) implements (\w+)\n\{/i';
        $replace = 'class ${1} extends ${2} implements ${3}, SignatureEntityInterface
{
    // Signature Behavior
    use SignatureEntityTrait;
';

        $script = preg_replace($pattern, $replace, $script);
    }

    public function modifyTable()
    {
        $table = $this->getTable();
        $documatic_signature_table = $table->getDatabase()->getTable('documatic_signature');

        $fk = new ForeignKey('FK_signature_entity');
        $fk->setForeignTableCommonName($table->getCommonName());
        $fk->setOnDelete(ForeignKey::CASCADE);
        $fk->addReference('entity_id', $table->getFirstPrimaryKeyColumn()->getName());
        $fk->setPhpName('Entity');

        $documatic_signature_table->addForeignKey($fk);
    }
}
