import { Link, useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AppLayout from "../../../../Layouts/AppLayout";
import Breadcrumb from "../../../../src/components/ui/Breadcrumb";
import Select from "react-select";
import TextInput from "../../../../src/components/ui/TextInput";
import TextAreaInput from "../../../../src/components/ui/TextAreaInput";
import Button from "../../../../src/components/ui/Button";
import { Icon } from "@iconify/react/dist/iconify.js";
import { notifyError, notifySuccess } from "../../../../src/components/ui/Toastify";
import MultipleFileUpload from "../../../../src/components/ui/MultipleFileUpload";
import ErrorInput from "../../../../src/components/ui/ErrorInput";
export default function IssueResolutionForm({ issue }) {
    const { t } = useTranslation()
    const { data, setData, post, errors, setError, clearErrors, processing, transform } = useForm({
        id: issue?.id ?? '',
        resolution_description: issue?.resolution?.resolution_description ?? '',
        gap_analysis: issue?.resolution?.gap_analysis ?? '',
        root_cause_analysis: issue?.resolution?.root_cause_analysis ?? '',
        corrective_action: issue?.resolution?.corrective_action ?? '',
        preventive_action: issue?.resolution?.preventive_action ?? '',
        issue_resolution_file: [],
        issue_resolution_file_urls: issue?.issueResolutionFileUrls,
    });
    const handleSubmit = (e) => {
        e.preventDefault();
        clearErrors();
        post(route('capa.resolution.store'), {
            onSuccess: (page) => {
                const { success, error } = page.props?.flash ?? {};
                if (success || error)
                    (success ? notifySuccess : notifyError)(success || error, 'bottom-center');
            },
        })
    }
    return (
        <AppLayout>
            <Breadcrumb title={t('issue_resolution')} subtitle={`${t('issue')} / ${t('add')}`} />
            <div className="container">
                <div className="card">
                    <div className="card-body">
                        <form onSubmit={handleSubmit} className="needs-validation" noValidate>
                            <div className='row gy-3 mb-3'>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('resolution_description')}</label>
                                    <TextAreaInput
                                        autoComplete="off"
                                        placeholder={t('enter_attribute', { 'attribute': t('resolution_description') })}
                                        value={data.resolution_description}
                                        onChange={(e) => setData('resolution_description', e.target.value)}
                                        errorMessage={errors.resolution_description}
                                    />
                                </div>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('gap_analysis')}</label>
                                    <TextAreaInput
                                        autoComplete="off"
                                        placeholder={t('enter_attribute', { 'attribute': t('gap_analysis') })}
                                        value={data.gap_analysis}
                                        onChange={(e) => setData('gap_analysis', e.target.value)}
                                        errorMessage={errors.gap_analysis}
                                    />
                                </div>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('root_cause_analysis')}</label>
                                    <TextAreaInput
                                        autoComplete="off"
                                        placeholder={t('enter_attribute', { 'attribute': t('root_cause_analysis') })}
                                        value={data.root_cause_analysis}
                                        onChange={(e) => setData('root_cause_analysis', e.target.value)}
                                        errorMessage={errors.root_cause_analysis}
                                    />
                                </div>
                              
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('preventive_action')}</label>
                                    <TextAreaInput
                                        autoComplete="off"
                                        placeholder={t('enter_attribute', { 'attribute': t('preventive_action') })}
                                        value={data.preventive_action}
                                        onChange={(e) => setData('preventive_action', e.target.value)}
                                        errorMessage={errors.preventive_action}
                                    />
                                </div>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('corrective_action')}</label>
                                    <TextAreaInput
                                        autoComplete="off"
                                        placeholder={t('enter_attribute', { 'attribute': t('corrective_action') })}
                                        value={data.corrective_action}
                                        onChange={(e) => setData('corrective_action', e.target.value)}
                                        errorMessage={errors.corrective_action}
                                    />
                                </div>
                                <div className='col-sm-12'>
                                    <label className='form-label'>{t('file')}</label>
                                    <div className='position-relative '>
                                        <MultipleFileUpload
                                            initialFileUrls={data.issue_resolution_file_urls}
                                            data={data}
                                            setData={setData}
                                            fieldName="issue_resolution_file"
                                            maxFiles={10}
                                            allowedFileTypes={['application/pdf']}
                                        />
                                    </div>
                                    {errors.issue_resolution_file && <ErrorInput message={errors.issue_resolution_file} className="text-danger" />}
                                </div>
                            </div>
                            <div className="col-12 mt-40">
                                <Link href={route('capa.issues.show', issue?.id)} className="btn btn-secondary me-20" isLoading={processing}><Icon icon="mdi:arrow-left" height={24} width={24} /> {t('back')}</Link>
                                <Button type="submit" className="btn btn-danger" isLoading={processing}><Icon icon="mdi:send-variant-outline" height={24} width={24} /> {t('submit')}</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}