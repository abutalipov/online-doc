<?php

namespace App\Admin\Controllers;


use App\Models\Signature;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Zxing\QrReader;

class VerifyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Example controller';

    public function index(Content $content)
    {
        return $content
            ->title('Верификация')
            ->description('Выберите файл с QR кодом')
            ->row(function (Row $row) {

                $row->column(3, function (Column $column) {
                });
                $row->column(6, function (Column $column) {
                    $form = new Form();
                    $form->action('verify');
                    $form->image('qr_file', "QR файл")->help('Выберите изображение')->required();
                    $column->append($form->render());
                });

                $row->column(3, function (Column $column) {
                });
            });
    }

    public function form()
    {
        $form = new Form();
        $form->image('qr_file');
    }

    public function store()
    {
        $request = \request();
        dd($request->file('qr_file')->path());
        $qrcode = new QrReader($request->file('qr_file')->path());
        $text = $qrcode->text(); //return decoded text from QR Code
        if ($text) {
            $signatures = Signature::where('uuid', $text)->first();
            if ($signatures) {
                $data = json_decode($signatures->data);
                $grid = new Table();
                $grid->setHeaders(['Ключ', 'Значение']);
                $rows = [];
                foreach ($data as $key => $data_item) {
                    $rows[] = [$key, $data_item];
                }
                $grid->setRows($rows);
                $render = $grid->render();
            } else {
                $render = "В базе не данных по данному идентификатору";
            }
        }else{
            $render = "Не удалось считать QR";
        }
        $row = new Content;
        $row->description('Результат проверки');
        $row->title('Верификация');
        return $row->row($render);


    }
}
