<?php
namespace App\Rptlib;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

    class RptExcel {
        public function ok() {
            return 'myFunction is OK';
            
            //     sample code to call the function
            //     use App\Rptlib\RptExcel;
            //     $RptExcel = new \App\Rptlib\RptExcel();
            //     dd($RptExcel->ok());
            
        }
        
        public function copyRange( Worksheet $sheet, $srcRange, $dstCell) {
            /**
             * Copy range in PHPSpreadsheet/PHPExcel including styles
             * sample: copyRange($sheet, 'A4:B8', 'E1');
             **/
            
            // Validate source range. Examples: A2:A3, A2:AB2, A27:B100
            if( !preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch) ) {
                // Wrong source range
                return;
            }
            // Validate destination cell. Examples: A2, AB3, A27
            if( !preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch) ) {
                // Wrong destination cell
                return;
            }
            
            $srcColumnStart = $srcRangeMatch[1];
            $srcRowStart = $srcRangeMatch[2];
            $srcColumnEnd = $srcRangeMatch[3];
            $srcRowEnd = $srcRangeMatch[4];
            
            $destColumnStart = $destCellMatch[1];
            $destRowStart = $destCellMatch[2];
            
            // For looping purposes we need to convert the indexes instead
            // Note: We need to subtract 1 since column are 0-based and not 1-based like this method acts.
            
            $srcColumnStart = Cell::columnIndexFromString($srcColumnStart) - 1;
            $srcColumnEnd = Cell::columnIndexFromString($srcColumnEnd) - 1;
            $destColumnStart = Cell::columnIndexFromString($destColumnStart) - 1;
            
            $rowCount = 0;
            for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
                $colCount = 0;
                for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $style = $sheet->getStyleByColumnAndRow($col, $row);
                    $dstCell = Cell::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                    $sheet->setCellValue($dstCell, $cell->getValue());
                    $sheet->duplicateStyle($style, $dstCell);
                    
                    // Set width of column, but only once per row
                    if ($rowCount === 0) {
                        $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                        $sheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setAutoSize(false);
                        $sheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setWidth($w);
                    }
                    
                    $colCount++;
                }
                
                $h = $sheet->getRowDimension($row)->getRowHeight();
                $sheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);
                
                $rowCount++;
            }
            
            foreach ($sheet->getMergeCells() as $mergeCell) {
                $mc = explode(":", $mergeCell);
                $mergeColSrcStart = Cell::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0])) - 1;
                $mergeColSrcEnd = Cell::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1])) - 1;
                $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
                $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));
                
                $relativeColStart = $mergeColSrcStart - $srcColumnStart;
                $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
                $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
                $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;
                
                if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                    $targetColStart = Cell::stringFromColumnIndex($destColumnStart + $relativeColStart);
                    $targetColEnd = Cell::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                    $targetRowStart = $destRowStart + $relativeRowStart;
                    $targetRowEnd = $destRowStart + $relativeRowEnd;
                    
                    $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                    //Merge target cells
                    $sheet->mergeCells($merge);
                }
            }
        }
        
    }


       
    