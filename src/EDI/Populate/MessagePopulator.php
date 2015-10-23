<?php
/**
 * Created by PhpStorm.
 * User: dominikkasprzak
 * Date: 23/10/15
 * Time: 10:02
 */

namespace EDI\Populate;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Mapping\MappingLoader;
use EDI\Message\Message;
use EDI\Message\MessageTrailer;

class MessagePopulator extends Populator
{
    /** @var  SegmentPopulator */
    private $segmentPopulator;
    /** @var  MappingLoader */
    private $mappingLoader;
    /** @var  string */
    private $mappingDirectory;

    public function __construct(AnnotationReader $annotationReader, SegmentPopulator $segmentPopulator, MappingLoader $mappingLoader, $mappingDir)
    {
        parent::__construct($annotationReader);
        $this->segmentPopulator = $segmentPopulator;
        $this->mappingLoader = $mappingLoader;
        $this->mappingDirectory = $mappingDir;
    }

    /**
     * @param array $data
     * @return Message[]
     * @throws \EDI\Exception\MandatorySegmentPieceMissing
     */
    public function populate(&$data)
    {
        $message = new Message();
        $this->fillProperties($message, $data);

        $identifier = $message->getIdentifier();
        $mapping = $this->mappingLoader->loadMessage(
            $this->mappingDirectory.
            sprintf(
                '/%s%s/messages/%s.xml',
                strtoupper($identifier['version']),
                strtoupper($identifier['release']),
                strtolower($identifier['type'])
            )
        );

        while (count($data) > 1) {
            $this->segmentPopulator->populate($data);
        }

        $trailer = new MessageTrailer();
        $this->fillProperties($trailer, $data);
        $message->setTrailer($trailer);

        return $message;
    }
}
