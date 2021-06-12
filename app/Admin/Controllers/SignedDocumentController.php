<?php

namespace App\Admin\Controllers;

use App\Models\DocumentTemplate;
use App\Models\SecretKey;
use App\Models\Signature;
use App\Models\SignedDocument;
use App\Models\User;
use App\Services\SignerService;
use BaconQrCode\Encoder\QrCode;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;

class SignedDocumentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Подписанные документы';
   protected function grid()
{
    $grid = new Grid(new SignedDocument());

    $grid->column('id', __('ID'))->sortable();
    $grid->column('user_id','Владелец');
    $grid->column('signature_key_id','Подпись (наверное в фоне генерировать)');
    $grid->column('document_template_id','Шаблон');
    $grid->column('path', 'Системный путь');

    return $grid;
}

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SignedDocument::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user_id','Владелец');
        $show->field('signature_key_id','Подпись');
        $show->field('document_template_id','Шаблон');
        $show->field('path', 'Системный путь')->file();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SignedDocument);
        $form->saving(function (Form $e){

            $uuid = Str::uuid();
            $user = Auth::user();
            $dataToSign = [];
            $signer = new SignerService();
            $document_template = DocumentTemplate::find($e->document_template_id);
            $template = Storage::disk('admin')->path($document_template->path);
            $fields = collect($e->fields)->where('_remove_',false)->pluck('value','field')->toArray();
            $dataToSign = array_merge($fields,$dataToSign);
            $signed = $signer->sign($dataToSign,'123');
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrPath = Storage::disk('local')->path('qr/'.$uuid.'.png');
            $writer->writeFile($uuid,$qrPath );

            $templateProcessor = new TemplateProcessor($template);
            $temp_vars = $templateProcessor->getVariables();
            foreach ($fields as $key => $field){
                $templateProcessor->setValue($key, $field);
                $search_key = array_search($key,$fields);
               // dump($search_key,$key,$fields);
                if($search_key and isset($temp_vars[$search_key])){
                    unset($temp_vars[$search_key]);
                }
            }
            if($temp_vars){
         //       dd($temp_vars,$e->fields);
            }

            $templateProcessor->setImageValue('eds_qr', array("path" => $qrPath, "width" => 150, "height" => 150));
            $signDocsPath = Storage::disk('local')->path('signDocs/'.$uuid.'.docx');
            $templateProcessor->saveAs($signDocsPath);


            $signature = new Signature();
            $signature->secret_key_id=$e->secret_key_id;
            $signature->user_id=$user->id;
            $signature->data=json_encode($dataToSign);
            $signature->signature=$signed;
            $signature->sign_at=Carbon::now();
            $signature->uuid=$uuid;
            $signature->save();
            $e->path = $signDocsPath;
            $e->user_id = $user->id;
            $e->signature_key_id = $signature->id;
            return $e;
        });

        $form->hidden('user_id');
        $form->hidden('signature_key_id');
        $form->hidden('path');
        $form->display('id', __('ID'));
        $form->select('secret_key_id','Ключ подписания')->options(SecretKey::all()->pluck('title','id'));
        $form->select('document_template_id','Шаблон')->options(DocumentTemplate::all()->pluck('name','id'));
        $form->table('fields',function($table){
            $table->text('field','Поле документа');
            $table->text('value','значение');
        });
        return $form;
    }
}
