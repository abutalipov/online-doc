<?php

namespace App\Admin\Controllers;

use App\Models\SecretKey;
use App\Models\Signature;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SignatureController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Подписи';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Signature());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('user_id','Инициатор');
        $grid->column('secret_key_id', 'Ключ подписи');
        $grid->column('signature', 'Подпись');
        $grid->column('data', 'Подписанные данные');
        $grid->column('sign_at', 'Подписан в');

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
        $show = new Show(Signature::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user_id','Инициатор');
        $show->field('secret_key_id', 'Ключ подписи');
        $show->field('data', 'Подписанные данные');
        $show->field('signature', 'Подпись');
        $show->field('sign_at', 'Подписан в');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Signature);

        $form->display('id', __('ID'));
        $form->select('user_id','Инициатор')->options(User::all()->pluck('name','id'));
        $form->select('secret_key_id', 'Ключ подписи')->options(SecretKey::all()->pluck('title','id'));
        $form->text('signature', 'Подпись');
        $form->text('data', 'Подписанные данные');
        $form->datetime('sign_at', 'Подписан в');
        return $form;
    }
}
