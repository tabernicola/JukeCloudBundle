<?php
namespace Tabernicola\JukeCloudBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Tabernicola\JukeCloudBundle\Entity\Disk;

class TextToDiskTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (disk) to a string (title).
     *
     * @param  Issue|null $disk
     * @return string
     */
    public function transform($disk)
    {
        if (null === $disk) {
            return "";
        }

        return $disk->getTitle();
    }

    /**
     * Transforms a string (title) to an object (disk).
     *
     * @param  string $title
     *
     * @return Issue|null
     *
     * @throws TransformationFailedException if object (disk) is not found.
     */
    public function reverseTransform($title)
    {
        if (!$title) {
            return null;
        }

        $disk = $this->om
            ->getRepository('TabernicolaJukeCloudBundle:Disk')
            ->findOneBy(array('title' => $title))
        ;

        if (null === $disk) {
            $disk=new Disk();
            $disk->setTitle($title);
        }

        return $disk;
    }
}
