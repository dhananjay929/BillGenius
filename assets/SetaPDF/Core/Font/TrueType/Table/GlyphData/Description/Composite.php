<?php
/**
 * This file is part of the SetaPDF-Core Component
 *
 * @copyright  Copyright (c) 2017 Setasign - Jan Slabon (https://www.setasign.com)
 * @category   SetaPDF
 * @package    SetaPDF_Core
 * @subpackage Font
 * @license    https://www.setasign.com/ Commercial
 * @version    $Id$
 */

/**
 * A class representing a composite glyph description.
 *
 * @copyright  Copyright (c) 2017 Setasign - Jan Slabon (https://www.setasign.com)
 * @category   SetaPDF
 * @package    SetaPDF_Core
 * @subpackage Font
 * @license    https://www.setasign.com/ Commercial
 */
class SetaPDF_Core_Font_TrueType_Table_GlyphData_Description_Composite
{
    /**
     * @var int
     */
    const FLAG_ARG_1_AND_2_ARE_WORDS = 0x0001;

    /**
     * @var int
     */
    const FLAG_ARGS_ARE_XY_VALUES = 0x0002;

    /**
     * @var int
     */
    const FLAG_ROUND_XY_TO_GRID = 0x0004;

    /**
     * @var int
     */
    const FLAG_WE_HAVE_A_SCALE = 0x0008;

    /**
     * @var int
     */
    const FLAG_MORE_COMPONENTS = 0x0020;

    /**
     * @var int
     */
    const FLAG_WE_HAVE_AN_X_AND_Y_SCALE = 0x0040;
    const FLAG_WE_HAVE_A_TWO_BY_TWO = 0x0080;
    const FLAG_WE_HAVE_INSTRUCTIONS = 0x0100;
    const FLAG_USE_MY_METRICS = 0x0200;
    const FLAG_OVERLAP_COMPOUND = 0x0400;
    const FLAG_SCALED_COMPONENT_OFFSET = 0x0800;
    const FLAG_UNSCALED_COMPONENT_OFFSET = 0x1000;

    /**
     * The glyph data table
     *
     * @var SetaPDF_Core_Font_TrueType_Table_GlyphData
     */
    protected $_glyphData;

    /**
     * Offset of this description
     *
     * @var integer
     */
    protected $_offset;

    /**
     * The constructor.
     *
     * @param SetaPDF_Core_Font_TrueType_Table_GlyphData $glyphData
     * @param integer $offset
     */
    public function __construct(SetaPDF_Core_Font_TrueType_Table_GlyphData $glyphData, $offset)
    {
        $this->_glyphData = $glyphData;
        $this->_offset = $offset;
    }

    /**
     * Release memory.
     */
    public function cleanUp()
    {
        $this->_glyphData = null;
    }

    /**
     * Read a value for this description.
     *
     * @param integer $offset
     * @param string $method
     * @return integer|mixed
     */
    private function _read($offset, $method = 'readInt16')
    {
        // TODO: Refactor to read from whole glyph data
        $record = $this->_glyphData->getRecord();
        $reader = $record->getFile()->getReader();
        $offset = $record->getOffset() + $this->_offset + $offset;

        return $reader->$method($offset);
    }

    /**
     * Returns all glyph ids from the composite.
     *
     * @return int[]
     */
    public function getGlyphIds()
    {
        $offset = 0;

        $result = [];

        do {
            // read the flags
            $flags = $this->_read($offset, 'readUInt16');
            $offset += 2;

            // read the glyphId
            $result[] = $this->_read($offset, 'readUInt16');

            if (($flags & self::FLAG_ARG_1_AND_2_ARE_WORDS) == self::FLAG_ARG_1_AND_2_ARE_WORDS) {
                // 2(glyphId) + (2 * 2) (argument1 & argument2)
                $offset += 6;
            } else {
                // 2(glyphId) + (1 * 2) (argument1 & argument2)
                $offset += 4;
            }
        } while (($flags & self::FLAG_MORE_COMPONENTS) == self::FLAG_MORE_COMPONENTS);

        return $result;
    }
}