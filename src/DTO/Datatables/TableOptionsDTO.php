<?php

namespace Zhortein\SymfonyToolboxBundle\DTO\Datatables;

class TableOptionsDTO
{
    public function __construct(
        public ColumnPartDTO $thead = new ColumnPartDTO(),
        public ColumnPartDTO $tbody = new ColumnPartDTO(),
        public ColumnPartDTO $tfoot = new ColumnPartDTO(),
    ) {
    }

    /**
     * @return array{
     *      thead: array{
     *        translate?: bool,
     *        keep_default_classes?: bool,
     *        class?: string,
     *        data?: array<string, string|int|float|bool|null>,
     *    },
     *      tbody: array{
     *        translate?: bool,
     *        keep_default_classes?: bool,
     *        class?: string,
     *        data?: array<string, string|int|float|bool|null>,
     *    },
     *      tfoot: array{
     *        translate?: bool,
     *        keep_default_classes?: bool,
     *        class?: string,
     *        data?: array<string, string|int|float|bool|null>,
     *    },
     *  }
     */
    public function toArray(): array
    {
        return [
            'thead' => $this->thead->toArray(),
            'tbody' => $this->tbody->toArray(),
            'tfoot' => $this->tfoot->toArray(),
        ];
    }

    /**
     * @param array{
     *     thead?: array{
     *       translate?: bool,
     *       keep_default_classes?: bool,
     *       class?: string,
     *       data?: array<string, string|int|float|bool|null>,
     *   },
     *     tbody?: array{
     *       translate?: bool,
     *       keep_default_classes?: bool,
     *       class?: string,
     *       data?: array<string, string|int|float|bool|null>,
     *   },
     *     tfoot?: array{
     *       translate?: bool,
     *       keep_default_classes?: bool,
     *       class?: string,
     *       data?: array<string, string|int|float|bool|null>,
     *   },
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            thead: isset($data['thead']) ? ColumnPartDTO::fromArray($data['thead']) : new ColumnPartDTO(),
            tbody: isset($data['tbody']) ? ColumnPartDTO::fromArray($data['tbody']) : new ColumnPartDTO(),
            tfoot: isset($data['tfoot']) ? ColumnPartDTO::fromArray($data['tfoot']) : new ColumnPartDTO()
        );
    }
}
