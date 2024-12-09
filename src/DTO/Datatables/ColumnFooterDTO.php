<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class ColumnFooterDTO extends ColumnPartDTO
{
    public const string AUTO_COUNT = 'count';
    public const string AUTO_SUM = 'sum';
    public const string AUTO_AVG = 'avg';
    public const string AUTO_MIN = 'min';
    public const string AUTO_MAX = 'max';
    public const string AUTO_NONE = '';

    public function __construct(
        bool $translate = false,
        bool $keepDefaultClasses = true,
        string $class = '',
        DataAttributeDTO $data = new DataAttributeDTO(),
        public string $auto = self::AUTO_NONE,
    ) {
        parent::__construct($translate, $keepDefaultClasses, $class, $data);
        if (!in_array($this->auto, [self::AUTO_COUNT, self::AUTO_SUM, self::AUTO_AVG, self::AUTO_MIN, self::AUTO_MAX], true)) {
            $this->auto = self::AUTO_NONE;
        }
    }

    /**
     * @return array{
     *       translate?: bool,
     *       keep_default_classes?: bool,
     *       class?: string,
     *       data?: array<string, string|int|float|bool|null>,
     *       auto?: string
     *   }
     */
    public function toArray(): array
    {
        $baseArray = parent::toArray();
        $baseArray['auto'] = $this->auto;

        return $baseArray;
    }

    /**
     * @param array{
     *      translate?: bool,
     *      keep_default_classes?: bool,
     *      class?: string,
     *      data?: array<string, string|int|float|bool|null>,
     *      auto?: string
     *  } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            translate: $data['translate'] ?? false,
            keepDefaultClasses: $data['keep_default_classes'] ?? true,
            class: $data['class'] ?? '',
            data: DataAttributeDTO::fromArray($data['data'] ?? []),
            auto: $data['auto'] ?? ''
        );
    }
}
