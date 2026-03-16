<?php

namespace App\Portal\Controllers;

use App\Models\CourtCase;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CourtCaseController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CourtCase(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('advocate_id');
            $grid->column('sub_user_id');
            $grid->column('advo_client_id');
            $grid->column('client_user');
            $grid->column('rights_id');
            $grid->column('source_type');
            $grid->column('case_sysno');
            $grid->column('case_name');
            $grid->column('case_level');
            $grid->column('case_profit_rate');
            $grid->column('case_profit_rate_code');
            $grid->column('client_position');
            $grid->column('handle_type');
            $grid->column('manage_id');
            $grid->column('party_name');
            $grid->column('party_lawyer');
            $grid->column('party_phone');
            $grid->column('party_country');
            $grid->column('party_state');
            $grid->column('party_city_id');
            $grid->column('party_address');
            $grid->column('dfdant_moreinfo');
            $grid->column('case_number');
            $grid->column('case_types');
            $grid->column('case_sub_type');
            $grid->column('case_status');
            $grid->column('case_sub_status');
            $grid->column('remarks');
            $grid->column('act');
            $grid->column('priority');
            $grid->column('court_no');
            $grid->column('court_type');
            $grid->column('court');
            $grid->column('judge_type');
            $grid->column('judge_name');
            $grid->column('filing_number');
            $grid->column('filing_date');
            $grid->column('registration_number');
            $grid->column('registration_date');
            $grid->column('remark');
            $grid->column('description');
            $grid->column('cnr_number');
            $grid->column('first_hearing_date');
            $grid->column('next_date');
            $grid->column('updated_by');
            $grid->column('police_station');
            $grid->column('gongz_number');
            $grid->column('fir_number');
            $grid->column('fir_date');
            $grid->column('is_nb');
            $grid->column('decision_date');
            $grid->column('nature_disposal');
            $grid->column('law_firm');
            $grid->column('lawfirm_id');
            $grid->column('lawyer_id');
            $grid->column('lawyer');
            $grid->column('extra_reward');
            $grid->column('extra_reward_ask');
            $grid->column('lawsuit_money');
            $grid->column('compensate_amount');
            $grid->column('compensate_pay_datetime');
            $grid->column('case_complete_tags');
            $grid->column('case_complete_sub_tags');
            $grid->column('selling_price');
            $grid->column('point');
            $grid->column('is_active');
            $grid->column('verify_status');
            $grid->column('pool_status');
            $grid->column('sale_status');
            $grid->column('is_brandnew');
            $grid->column('tags');
            $grid->column('is_lvshihan');
            $grid->column('is_plaint');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new CourtCase(), function (Show $show) {
            $show->field('id');
            $show->field('advocate_id');
            $show->field('sub_user_id');
            $show->field('advo_client_id');
            $show->field('client_user');
            $show->field('rights_id');
            $show->field('source_type');
            $show->field('case_sysno');
            $show->field('case_name');
            $show->field('case_level');
            $show->field('case_profit_rate');
            $show->field('case_profit_rate_code');
            $show->field('client_position');
            $show->field('handle_type');
            $show->field('manage_id');
            $show->field('party_name');
            $show->field('party_lawyer');
            $show->field('party_phone');
            $show->field('party_country');
            $show->field('party_state');
            $show->field('party_city_id');
            $show->field('party_address');
            $show->field('dfdant_moreinfo');
            $show->field('case_number');
            $show->field('case_types');
            $show->field('case_sub_type');
            $show->field('case_status');
            $show->field('case_sub_status');
            $show->field('remarks');
            $show->field('act');
            $show->field('priority');
            $show->field('court_no');
            $show->field('court_type');
            $show->field('court');
            $show->field('judge_type');
            $show->field('judge_name');
            $show->field('filing_number');
            $show->field('filing_date');
            $show->field('registration_number');
            $show->field('registration_date');
            $show->field('remark');
            $show->field('description');
            $show->field('cnr_number');
            $show->field('first_hearing_date');
            $show->field('next_date');
            $show->field('updated_by');
            $show->field('police_station');
            $show->field('gongz_number');
            $show->field('fir_number');
            $show->field('fir_date');
            $show->field('is_nb');
            $show->field('decision_date');
            $show->field('nature_disposal');
            $show->field('law_firm');
            $show->field('lawfirm_id');
            $show->field('lawyer_id');
            $show->field('lawyer');
            $show->field('extra_reward');
            $show->field('extra_reward_ask');
            $show->field('lawsuit_money');
            $show->field('compensate_amount');
            $show->field('compensate_pay_datetime');
            $show->field('case_complete_tags');
            $show->field('case_complete_sub_tags');
            $show->field('selling_price');
            $show->field('point');
            $show->field('is_active');
            $show->field('verify_status');
            $show->field('pool_status');
            $show->field('sale_status');
            $show->field('is_brandnew');
            $show->field('tags');
            $show->field('is_lvshihan');
            $show->field('is_plaint');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new CourtCase(), function (Form $form) {
            $form->display('id');
            $form->text('advocate_id');
            $form->text('sub_user_id');
            $form->text('advo_client_id');
            $form->text('client_user');
            $form->text('rights_id');
            $form->text('source_type');
            $form->text('case_sysno');
            $form->text('case_name');
            $form->text('case_level');
            $form->text('case_profit_rate');
            $form->text('case_profit_rate_code');
            $form->text('client_position');
            $form->text('handle_type');
            $form->text('manage_id');
            $form->text('party_name');
            $form->text('party_lawyer');
            $form->text('party_phone');
            $form->text('party_country');
            $form->text('party_state');
            $form->text('party_city_id');
            $form->text('party_address');
            $form->text('dfdant_moreinfo');
            $form->text('case_number');
            $form->text('case_types');
            $form->text('case_sub_type');
            $form->text('case_status');
            $form->text('case_sub_status');
            $form->text('remarks');
            $form->text('act');
            $form->text('priority');
            $form->text('court_no');
            $form->text('court_type');
            $form->text('court');
            $form->text('judge_type');
            $form->text('judge_name');
            $form->text('filing_number');
            $form->text('filing_date');
            $form->text('registration_number');
            $form->text('registration_date');
            $form->text('remark');
            $form->text('description');
            $form->text('cnr_number');
            $form->text('first_hearing_date');
            $form->text('next_date');
            $form->text('updated_by');
            $form->text('police_station');
            $form->text('gongz_number');
            $form->text('fir_number');
            $form->text('fir_date');
            $form->text('is_nb');
            $form->text('decision_date');
            $form->text('nature_disposal');
            $form->text('law_firm');
            $form->text('lawfirm_id');
            $form->text('lawyer_id');
            $form->text('lawyer');
            $form->text('extra_reward');
            $form->text('extra_reward_ask');
            $form->text('lawsuit_money');
            $form->text('compensate_amount');
            $form->text('compensate_pay_datetime');
            $form->text('case_complete_tags');
            $form->text('case_complete_sub_tags');
            $form->text('selling_price');
            $form->text('point');
            $form->text('is_active');
            $form->text('verify_status');
            $form->text('pool_status');
            $form->text('sale_status');
            $form->text('is_brandnew');
            $form->text('tags');
            $form->text('is_lvshihan');
            $form->text('is_plaint');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
