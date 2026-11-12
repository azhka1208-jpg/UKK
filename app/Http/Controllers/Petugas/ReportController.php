<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports)
    {
    }

    public function index(Request $request)
    {
        $type = $this->resolveType($request);
        $filters = $this->filters($request);
        $report = $this->reports->build($type, $filters);
        $categories = Category::orderBy('name')->get();
        $types = ReportService::TYPES;

        return view('petugas.reports.index', compact('report', 'filters', 'type', 'categories', 'types'));
    }

    public function print(Request $request)
    {
        $type = $this->resolveType($request);
        $report = $this->reports->build($type, $this->filters($request));

        return view('petugas.reports.print', compact('report'));
    }

    public function pdf(Request $request)
    {
        $type = $this->resolveType($request);
        $report = $this->reports->build($type, $this->filters($request));

        $pdf = Pdf::loadView('petugas.reports.print', compact('report'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-' . $type . '-' . now()->format('Ymd-His') . '.pdf');
    }

    public function excel(Request $request)
    {
        $type = $this->resolveType($request);
        $report = $this->reports->build($type, $this->filters($request));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($report['meta']['title'], 0, 31));

        $sheet->setCellValue('A1', $report['meta']['title']);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('A2', 'Periode: ' . $report['meta']['period']);
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A3', 'Dibuat: ' . $report['meta']['generated_at']);
        $sheet->mergeCells('A3:F3');

        $headerRow = 5;
        $sheet->fromArray($report['columns'], null, 'A' . $headerRow);

        $lastColumn = chr(ord('A') + count($report['columns']) - 1);
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1E3A5F');
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');

        $dataRow = $headerRow + 1;
        foreach ($report['rows'] as $row) {
            $sheet->fromArray($row, null, 'A' . $dataRow);
            $dataRow++;
        }

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('A' . ($headerRow + 1));

        $filename = 'laporan-' . $type . '-' . now()->format('Ymd-His') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'sistema-xlsx-') . '.xlsx';

        (new Xlsx($spreadsheet))->save($temp);
        $spreadsheet->disconnectWorksheets();

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function resolveType(Request $request): string
    {
        $type = $request->get('type', 'tools');

        return array_key_exists($type, ReportService::TYPES) ? $type : 'tools';
    }

    private function filters(Request $request): array
    {
        return [
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'category' => $request->get('category'),
            'status' => $request->get('status'),
            'action' => $request->get('action'),
        ];
    }
}