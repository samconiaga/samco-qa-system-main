import { useTranslation } from "react-i18next";
import TextInput from "../../../../src/components/ui/TextInput";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";
import Button from "../../../../src/components/ui/Button";
import { useState } from "react";

export default function HalalAndRegulatoryForm({ data, setData, errors, setError, method }) {
    const { t } = useTranslation();
    const post = method;
    const [isLoading, setIsLoading] = useState(false);
    const handleSubmit = async () => {
        setIsLoading(true);
        post(route('change-requests.halal-and-regulatory-evaluation', data.change_request_id), {
            scroll: false,
            onSuccess: () => {
                post(route('change-requests.approve', data.change_request_id), {
                    scroll: false,
                    onSuccess: () => setIsLoading(false),
                    onError: () => setIsLoading(false),
                });
            },
            onError: () => {
                setIsLoading(false);
            },
        });
    }
    return (
        <div className='row gy-3'>
          
            <div className='form-group d-flex align-items-center justify-content-end gap-8'>
                <Button
                    onClick={handleSubmit}
                    type='button'
                    className='form-wizard-next-btn btn btn-success-600 px-32'
                >
                    {t('submit')}
                </Button>
            </div>

        </div>
    )
}
