import { usePage } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import TextInput from "../../../../src/components/ui/TextInput";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";
import { route } from "ziggy-js";
import ErrorInput from "../../../../src/components/ui/ErrorInput";
import TypeOfChange from "./TypeOfChange";
import MultipleFileUpload from "../../../../src/components/ui/MultipleFileUpload";
import TextAreaInput from "../../../../src/components/ui/TextAreaInput";
import Button from "../../../../src/components/ui/Button";
import EditableFileUpload from "../../../../src/components/ui/EditableFileUpload/EditableFileUpload";


export default function ChangeInitiationForm({ data, setData, errors, setError, ...props }) {
    const { t } = useTranslation();
    return (
        <>

            <div className='row gy-3'>
                <div className='col-sm-12'>
                    <label className='form-label'>{t('title_of_change')}</label>
                    <div className='position-relative'>
                        <TextInput
                            className="form-control"
                            autoComplete="off"
                            onChange={(e) => setData('title', e.target.value.toUpperCase())}
                            placeholder={t('enter_attribute', { 'attribute': t('title_of_change') })}
                            style={{ height: '60px', textTransform: 'uppercase' }}
                            value={data.title}
                            errorMessage={errors.title}
                            autoFocus
                        />
                    </div>
                </div>
                <div className='col-sm-6'>
                    <label className='form-label'>{t('email')}</label>
                    <div className='position-relative'>
                        <TextInput
                            type='email'
                            className="form-control"
                            autoComplete="off"
                            placeholder={t('enter_attribute', { 'attribute': t('email') })}
                            value={data.email}
                            errorMessage={errors.email}
                            readOnly
                        />
                    </div>
                </div>
                <div className='col-sm-6'>
                    <label className='form-label'>{t('initiator_name')}</label>
                    <div className='position-relative'>
                        <TextInput
                            type='text'
                            className="form-control"
                            autoComplete="off"
                            placeholder={t('enter_attribute', { 'attribute': t('initiator_name') })}
                            value={data.initiator_name}
                            onChange={(e) => setData('initiator_name', e.target.value)}
                            errorMessage={errors.initiator_name}
                        />
                    </div>
                </div>
                <hr />
                <div className='col-12'>
                    <label className='form-label'>{t('related_change')}</label>

                    {props.scopes.map((scope, index) => (

                        <div key={scope.id} className='form-check style-check d-flex align-items-center col-3 mt-20' >
                            <CheckBoxInput
                                id={`scope-${scope.id}`}
                                errorMessage={errors.scopes}
                                errorInput={false} className="checked-danger border border-neutral-300"
                                checked={data.scopes.includes(scope.name)}
                                label={scope.name}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('scopes', [...data.scopes, scope.name]);
                                        setError('scopes', '');
                                    } else {
                                        setData('scopes', data.scopes.filter(name => name !== scope.name));
                                    }
                                }} />
                        </div>

                    ))}
                </div>
                <hr />
                <TypeOfChange data={data} setData={setData} errors={errors} />
                <hr />
                <div className='col-sm-6'>
                    <label className='form-label'>{t('current_status')}</label>
                    <div className='position-relative'>
                        <TextAreaInput
                            autoComplete="off"
                            placeholder={t('enter_attribute', { 'attribute': t('current_status') })}
                            value={data.current_status}
                            onChange={(e) => setData('current_status', e.target.value)}
                            errorMessage={errors.current_status}
                        />
                    </div>
                </div>

                <div className='col-sm-6'>
                    <label className='form-label'>{t('proposed_change')}</label>
                    <div className='position-relative'>
                        <TextAreaInput
                            autoComplete="off"
                            placeholder={t('enter_attribute', { 'attribute': t('proposed_change') })}
                            value={data.proposed_change}
                            onChange={(e) => setData('proposed_change', e.target.value)}
                            errorMessage={errors.proposed_change}
                        />
                    </div>
                </div>
                <div className='col-sm-12'>
                    <label className='form-label'>{t('current_status_file')}</label>
                    <div className='position-relative '>
                        <EditableFileUpload
                            existingFiles={data.current_status_files_meta}
                            onChange={files => setData('current_status_file', files)}
                            onKeepChange={ids => setData('current_status_file_keep_ids', ids)}
                            onNamesChange={names => setData('current_status_file_names', names)}
                            maxFiles={10}
                            allowedFileTypes={['.pdf']}
                        />
                    </div>
                    {errors.current_status_file && <ErrorInput message={errors.current_status_file} className="text-danger" />}
                </div>
                <div className='col-sm-12'>
                    <label className='form-label'>{t('proposed_change_file')}</label>
                    <div className='position-relative'>
                        <EditableFileUpload
                            existingFiles={data.proposed_change_files_meta}
                            onChange={files => setData('proposed_change_file', files)}
                            onKeepChange={ids => setData('proposed_change_file_keep_ids', ids)}
                            onNamesChange={names => setData('proposed_change_file_names', names)}
                            maxFiles={10}
                            allowedFileTypes={['.pdf']}
                        />
                        {errors.proposed_change_file && <ErrorInput message={errors.proposed_change_file} className="text-danger" />}
                    </div>
                </div>

                <div className='col-sm-12'>
                    <label className='form-label'>{t('change_reason')}</label>
                    <div className='position-relative'>
                        <TextAreaInput
                            autoComplete="off"
                            placeholder={t('enter_attribute', { 'attribute': t('change_reason') })}
                            value={data.reason}
                            onChange={(e) => setData('reason', e.target.value)}
                            errorMessage={errors.reason}
                        />
                    </div>
                </div>

                <div className='col-sm-12'>
                    <label className='form-label'>{t('supporting_attachment')}</label>
                    <div className='position-relative'>
                        <EditableFileUpload
                            existingFiles={data.supporting_attachments_meta}
                            onChange={files => setData('supporting_attachment', files)}
                            onKeepChange={ids => setData('supporting_attachment_keep_ids', ids)}
                            onNamesChange={names => setData('supporting_attachment_names', names)}
                            maxFiles={10}
                            allowedFileTypes={['.pdf']}
                        />

                    </div>
                    {errors.supporting_attachment && <ErrorInput message={errors.supporting_attachment} className="text-danger" />}
                </div>
            </div >
        </>
    );
}
