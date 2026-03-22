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
 * A class representing a simple glyph description.
 *
 * @copyright  Copyright (c) 2017 Setasign - Jan Slabon (https://www.setasign.com)
 * @category   SetaPDF
 * @package    SetaPDF_Core
 * @subpackage Font
 * @license    https://www.setasign.com/ Commercial
 */
class SetaPDF_Core_Font_TrueType_Table_GlyphData_Description_Simple
{
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
}