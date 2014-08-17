<?php
namespace Tabernicola\JukeCloudBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Tabernicola\JukeCloudBundle\Entity\Artist;

class TextToArtistTransformer implements DataTransformerInterface
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
     * Transforms an object (artist) to a string (name).
     *
     * @param  Issue|null $artist
     * @return string
     */
    public function transform($artist)
    {
        if (null === $artist) {
            return "";
        }

        return $artist->getName();
    }

    /**
     * Transforms a string (name) to an object (artist).
     *
     * @param  string $name
     *
     * @return Issue|null
     *
     * @throws TransformationFailedException if object (artist) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $artist = $this->om
            ->getRepository('TabernicolaJukeCloudBundle:Artist')
            ->findOneBy(array('name' => $name))
        ;

        if (null === $artist) {
            $artist=new Artist();
            $artist->setName($name);
        }

        return $artist;
    }
}
