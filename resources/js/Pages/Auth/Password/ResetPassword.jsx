import { Link, useForm } from "@inertiajs/react";
import AuthLayout from "../../../Layouts/AuthLayout";
import { useTranslation } from "react-i18next";
import { Icon } from "@iconify/react/dist/iconify.js";
import TextInput from "../../../src/components/ui/TextInput";
import Button from "../../../src/components/ui/Button";

export default function ResetPassword({ title }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, clearErrors } = useForm({
        email: '',
    });
    const handleSubmit = async (e) => {
        e.preventDefault();
        clearErrors();
        post(route('reset-password.store'));
    };
    return (
        <>
            <AuthLayout>
                <div className='auth-right py-32 px-24 d-flex flex-column justify-content-center'>
                    <div className='max-w-464-px mx-auto w-100'>
                        <div className="text-center">
                            <Link href='/' className='mb-20 max-w-100-px item d-inline-block'>
                                <img src='/assets/images/logo.png' alt='' className="img-fluid"></img>
                            </Link>
                            <h4 className='mb-12 text-center'>{title}</h4>
                        </div>
                        <form action='#' onSubmit={handleSubmit} className="needs-validation mt-5" noValidate>
                            <label htmlFor="email" className="form-label">{t("email")}</label>
                            <div className='icon-field has-validation mb-16'>
                                <span className='icon mt-2'>
                                    <Icon icon='mage:email' />
                                </span>
                                <TextInput
                                    name="email"
                                    type='email'
                                    onChange={(e) => setData('email', e.target.value)}
                                    className='bg-neutral-50 radius-12'
                                    placeholder={t('enter_email')}
                                    autoComplete="off"
                                    errorMessage={errors.email}
                                />
                            </div>
                            <Button type="submit" className="btn btn-danger text-sm px-12 py-16 w-100 radius-12 mt-32 d-flex align-items-center justify-content-center" isLoading={processing}>
                                {t('submit')}
                            </Button>
                        </form>
                    </div>
                </div>
            </AuthLayout >
        </>
    )
}