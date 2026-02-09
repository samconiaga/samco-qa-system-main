import { useTranslation } from "react-i18next";
import AppLayout from "../../../../Layouts/AppLayout";
import Breadcrumb from "../../../../src/components/ui/Breadcrumb";
import TextInput from "../../../../src/components/ui/TextInput";

import SelectInput from "../../../../src/components/ui/SelectInput";
import { useEffect, useRef } from "react";
import TextAreaInput from "../../../../src/components/ui/TextAreaInput";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";
import Button from "../../../../src/components/ui/Button";
import { useForm, usePage } from "@inertiajs/react";
import ErrorInput from "../../../../src/components/ui/ErrorInput";
import { notifyError } from "../../../../src/components/ui/Toastify";
import { route } from "ziggy-js";

export default function Form({ positions, departments, ...props }) {
    const { t } = useTranslation();
    const { data, setData, post, put, processing, reset, errors, setError } = useForm({
        name: props?.employee?.name ?? '',
        employee_code: props?.employee?.employee_code ?? '',
        email: props?.employee?.user?.email ?? '',
        position_id: props?.employee?.position_id ?? '',
        department_id: props?.employee?.department_id ?? '',
        phone: props?.employee?.phone ?? '',
        gender: props?.employee?.gender ?? '',
        address: props?.employee?.address ?? '',
        permissions: props.employee?.user?.permissions?.map(p => p?.name) ?? [],
    });

    const nameRef = useRef();
    const emailRef = useRef();
    const positionRef = useRef();
    const genderRef = useRef();
    const phoneRef = useRef();
    const addressRef = useRef();
    const isEdit = !!props.employee;
    const { flash } = usePage().props;
    useEffect(() => {
        console.log(flash);

        if (flash.error) {
            notifyError(flash.error, 'bottom-center');
        }
    }, [flash.error]);


    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(route('master-data.employees.update', props.employee.id))
        } else {
            post(route('master-data.employees.store'))
        }
    };

    return (
        <>
            <AppLayout>
                <Breadcrumb title={props.title} subtitle={`${t('master_data')} / ${t('employees')} / ${isEdit ? t('edit') : t('add')}`} />
                <div className="container">
                    <div className="col-md-12">
                        <div className="card">

                            <div className="card-body">
                                <form onSubmit={handleSubmit} className="needs-validation" noValidate>
                                    <div className="row gy-3">
                                        <div className="col-4">
                                            <label className="form-label">{t('name')}</label>
                                            <TextInput name="name"
                                                className="form-control"
                                                autoComplete="off"
                                                ref={nameRef}
                                                onChange={(e) => setData('name', e.target.value)}
                                                placeholder={t('enter_attribute', { 'attribute': t('employee_name') })}
                                                value={data.name}
                                                errorMessage={errors.name}
                                                autoFocus
                                            />
                                        </div>
                                        <div className="col-4">
                                            <label className="form-label">{t('employee_code')}</label>
                                            <TextInput name="employee_code"
                                                className="form-control"
                                                autoComplete="off"
                                                onChange={(e) => setData('employee_code', e.target.value)}
                                                placeholder={t('enter_attribute', { 'attribute': t('employee_code') })}
                                                value={data.employee_code}
                                                errorMessage={errors.employee_code}
                                                autoFocus
                                            />
                                        </div>
                                        <div className="col-4">
                                            <label className="form-label">{t('email')}</label>
                                            <TextInput name="email"
                                                type="email"
                                                className="form-control"
                                                ref={emailRef}
                                                autoComplete="off"
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder={t('enter_attribute', { 'attribute': t('email') })}
                                                value={data.email}
                                                errorMessage={errors.email} />
                                        </div>
                                        <div className="col-6">
                                            <label className="form-label">{t('gender')}</label>
                                            <SelectInput name="gender"
                                                className="form-control"
                                                ref={genderRef}
                                                onChange={(e) => setData('gender', e.target.value)}
                                                value={data.gender}
                                                errorMessage={errors.gender} >
                                                <option value="" disabled>{t('select_gender')}</option>
                                                <option value="male">{t('male')}</option>
                                                <option value="female">{t('female')}</option>
                                            </SelectInput>
                                        </div>
                                        <div className="col-6">
                                            <label className="form-label">{t('phone')}</label>
                                            <TextInput
                                                name="phone"
                                                className="form-control"
                                                ref={phoneRef}
                                                onChange={(e) => {
                                                    const value = e.target.value;
                                                    // Hanya izinkan angka, spasi, dan tanda +
                                                    const filtered = value.replace(/[^0-9+]/g, '');
                                                    setData('phone', filtered);
                                                }}
                                                placeholder={t('enter_attribute', { 'attribute': t('phone') })}
                                                value={data.phone}
                                                autoComplete="off"
                                                errorMessage={errors.phone}
                                            />
                                        </div>
                                        <div className="col-6">
                                            <label className="form-label">{t('position')}</label>
                                            <SelectInput name="position_id"
                                                className="form-control"
                                                ref={positionRef}
                                                onChange={(e) => setData('position_id', e.target.value)}
                                                value={data.position_id}
                                                errorMessage={errors.position_id} >
                                                <option value="" disabled>{t('select_position')}</option>
                                                {positions.map((position) => (
                                                    <option key={position.id} value={position.id}>
                                                        {position.name}
                                                    </option>
                                                ))}
                                            </SelectInput>
                                        </div>
                                        <div className="col-6">
                                            <label className="form-label">{t('department')}</label>
                                            <SelectInput name="department_id"
                                                className="form-control"
                                                ref={positionRef}
                                                onChange={(e) => setData('department_id', e.target.value)}
                                                value={data.department_id}
                                                errorMessage={errors.department_id} >
                                                <option value="" disabled>{t('select_department')}</option>
                                                {departments.map((department) => (
                                                    <option key={department.id} value={department.id}>
                                                        {department.name}
                                                    </option>
                                                ))}
                                            </SelectInput>
                                        </div>

                                        <div className="col-12">
                                            <label htmlFor="address">{t('address')}</label>
                                            <TextAreaInput
                                                name="address"
                                                ref={addressRef}
                                                onChange={(e) => setData('address', e.target.value)}
                                                value={data.address}
                                                placeholder={t('enter_attribute', { 'attribute': t('address') })}
                                                autoComplete="off"
                                                style={{ height: '150px' }}
                                                errorMessage={errors.address}
                                            />
                                        </div>

                                        <div className="col-12">
                                            <label htmlFor="permission">{t('permissions')}</label>
                                            <div className="row">
                                                {props.permissions.map((permission) => (
                                                    <div key={permission.id} className='form-check style-check d-flex align-items-center col-3 mt-20'>
                                                        <CheckBoxInput
                                                            id={`permission_${permission.id}`}
                                                            className="checked-danger border border-neutral-300"
                                                            checked={data.permissions.includes(permission.name)}
                                                            label={permission.name}
                                                            onChange={(e) => {
                                                                if (e.target.checked) {
                                                                    setData('permissions', [...data.permissions, permission.name]);
                                                                    setError('permissions', '');
                                                                    console.log(...data.permissions);

                                                                } else {
                                                                    setData('permissions', data.permissions.filter(name => name !== permission.name));
                                                                    console.log(...data.permissions);
                                                                }
                                                            }}
                                                            errorMessage={errors.permissions}
                                                            errorInput={false}
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                        {errors.permissions && <ErrorInput message={errors.permissions} className="text-danger" />}
                                        <div className="col-12 mt-40">
                                            <Button type="submit" className="btn btn-danger" isLoading={processing}>{t('save')}</Button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        {/* card end */}
                    </div>
                </div>
            </AppLayout>
        </>
    );
}