import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AppLayout from "../../../../Layouts/AppLayout";
import Breadcrumb from "../../../../src/components/ui/Breadcrumb";
import Select from "react-select";
import TextInput from "../../../../src/components/ui/TextInput";
import TextAreaInput from "../../../../src/components/ui/TextAreaInput";
import Button from "../../../../src/components/ui/Button";
import { Icon } from "@iconify/react/dist/iconify.js";
import { notifyError, notifySuccess } from "../../../../src/components/ui/Toastify";
export default function IssueForm({ departments, capaTypes, issue }) {
    const { t } = useTranslation()
    const { data, setData, post, errors, setError, clearErrors, processing, transform } = useForm({
        id: issue?.id ?? '',
        department_id: issue?.department_id ?? '',
        capa_type_id: issue?.capa_type_id ?? '',
        deadline: issue?.deadline ?? '',
        subject: issue?.subject ?? '',
        finding: issue?.finding ?? '',
        criteria: issue?.criteria ?? '',
        issue_number: issue?.issue_number ?? ''
    });
    const criteria = [
        { value: 'Minor', label: 'Minor' },
        { value: 'Major', label: 'Major' },
        { value: 'Critical', label: 'Critical' },
    ];

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('capa.issues.store'), {
            onSuccess: (page) => {
                const { success, error } = page.props?.flash ?? {};
                if (success || error)
                    (success ? notifySuccess : notifyError)(success || error, 'bottom-center');
            },
        })
    }
    return (
        <AppLayout>
            <Breadcrumb title={t('add_issue')} subtitle={`${t('issue')} / ${t('add')}`} />
            <div className="container">
                <div className="card">
                    <div className="card-body">
                        <form onSubmit={handleSubmit} className="needs-validation" noValidate>
                            <div className='row gy-3 mb-3'>
                                <div className='col-sm-6'>
                                    <label className='form-label'>{t('department')}</label>
                                    <Select
                                        options={departments}
                                        getOptionLabel={(item) => item.name}
                                        getOptionValue={(item) => item.id}
                                        value={
                                            departments.find(
                                                (opt) => opt.id === data.department_id
                                            ) || null
                                        }
                                        onChange={(val) => {
                                            clearErrors('department_id');
                                            setData("department_id", val?.id);
                                        }}
                                        placeholder={t("select_attribute", { attribute: t('department') })}
                                        isClearable
                                        isSearchable
                                    />
                                    {errors?.department_id && (
                                        <div className="text-danger mt-2">
                                            {errors?.department_id}
                                        </div>
                                    )}
                                </div>
                                <div className='col-sm-6'>
                                    <label className='form-label'>{t('capa_type')}</label>
                                    <Select
                                        options={capaTypes}
                                        getOptionLabel={(item) => item.name}
                                        getOptionValue={(item) => item.id}
                                        value={
                                            capaTypes.find(
                                                (opt) => opt.id === data.capa_type_id
                                            ) || null
                                        }
                                        onChange={(val) => {
                                            clearErrors('capa_type_id');
                                            setData("capa_type_id", val?.id);
                                        }}
                                        placeholder={t("select_attribute", { attribute: t('capa_type') })}
                                        isClearable
                                        isSearchable
                                    />
                                    {errors?.capa_type_id && (
                                        <div className="text-danger mt-2">
                                            {errors?.capa_type_id}
                                        </div>
                                    )}
                                </div>
                            </div>
                            <div className='row gy-3 mb-3'>
                                <div className='col-sm-6'>
                                    <label className='form-label'>{t('criteria')}</label>
                                    <Select
                                        options={criteria}
                                        getOptionLabel={(item) => item.label}
                                        getOptionValue={(item) => item.value}
                                        value={criteria.find((opt) => opt.value === data.criteria) || null}
                                        onChange={(val) => setData('criteria', val?.value)}
                                        placeholder={t("select_attribute", { attribute: t('criteria') })}
                                        isClearable
                                        isSearchable
                                    />

                                    {errors?.criteria && (
                                        <div className="text-danger mt-2">
                                            {errors?.criteria}
                                        </div>
                                    )}
                                </div>
                                <div className='col-sm-6'>
                                    <label className='form-label'>{t('deadline')}</label>
                                    <TextInput
                                        type="datetime-local"
                                        value={data.deadline || ""}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setData("deadline", value);
                                        }}
                                        placeholder={t("enter_attribute", {
                                            attribute: t("deadline"),
                                        })}
                                        errorMessage={errors?.deadline}
                                    />
                                </div>
                            </div>
                            <div className='row gy-3 mb-3'>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('subject')}</label>
                                    <TextInput
                                        className="form-control"
                                        autoComplete="off"
                                        onChange={(e) => setData('subject', e.target.value.toUpperCase())}
                                        placeholder={t('enter_attribute', { 'attribute': t('subject') })}
                                        style={{ height: '60px', textTransform: 'uppercase' }}
                                        value={data.subject}
                                        errorMessage={errors.subject}
                                    />
                                </div>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('findings')}</label>
                                    <TextAreaInput
                                        autoComplete="off"
                                        placeholder={t('enter_attribute', { 'attribute': t('finding') })}
                                        value={data.finding}
                                        onChange={(e) => setData('finding', e.target.value)}
                                        errorMessage={errors.finding}
                                    />
                                </div>
                            </div>
                            <div className="col-12 mt-40">
                                <Button type="submit" className="btn btn-danger" isLoading={processing}><Icon icon="mdi:send-variant-outline" height={24} width={24} /> {t('submit')}</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}