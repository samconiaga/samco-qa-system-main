import { useTranslation } from "react-i18next";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";
import { useEffect, useState } from "react";
import TextInput from "../../../../src/components/ui/TextInput";

export default function TypeOfChange({ data, setData, errors }) {
    const { t } = useTranslation();
    const MASTER_TYPES = [
        'Continual improvement / Inovasi',
        'Transfer Teknologi',
        'Peralatan / Mesin',
        'Supplier',
        'Alih Daya / Perubahan Kepemilikan Produk',
        'Aspek Pembuatan Produk',
        'Penyesuaian Regulasi / Monografi',
        'Perubahan Proses Bisnis',
        'Relokasi Fasilitas / Pabrik',
        'Dokumen'
    ];
    const [otherValue, setOtherValue] = useState('');
    const [otherChecked, setOtherChecked] = useState(false);

    useEffect(() => {
        const other = data.type_of_change.find(t => !MASTER_TYPES.includes(t));

        if (other) {
            setOtherChecked(true);
            setOtherValue(other);
        } else {
            setOtherChecked(false);
            setOtherValue('');
        }
    }, [data.type_of_change]);
    return (
        <>
            <div className='col-12'>
                <label className='form-label'>{t('type_of_change')}</label>
                <div className="row">
                    <div className="col-md-6">
                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_1"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Continual improvement / Inovasi')}
                                label={t('continual_improvement_or_inovation')}
                                value={'Continual improvement / Inovasi'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_2"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Transfer Teknologi')}
                                label={t('technology_transfer')}
                                value={'Transfer Teknologi'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_3"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Peralatan / Mesin')}
                                label={t('equipment_or_machine')}
                                value={'Peralatan / Mesin'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_4"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Supplier')}
                                label={t('supplier')}
                                value={'Supplier'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_5"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Alih Daya / Perubahan Kepemilikan Produk')}
                                label={t('outsourcing_or_change_of_product_ownership')}
                                value={'Alih Daya / Perubahan Kepemilikan Produk'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>
                    </div>

                    <div className="col-md-6">
                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_6"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Aspek Pembuatan Produk')}
                                label={t('product_manufacturing_aspect')}
                                value={'Aspek Pembuatan Produk'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_7"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Penyesuaian Regulasi / Monografi')}
                                label={t('regulation_or_monograph_adjustment')}
                                value={'Penyesuaian Regulasi / Monografi'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_8"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Perubahan Proses Bisnis')}
                                label={t('business_process_change')}
                                value={'Perubahan Proses Bisnis'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>

                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_9"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Relokasi Fasilitas / Pabrik')}
                                label={t('facility_or_factory_relocation')}
                                value={'Relokasi Fasilitas / Pabrik'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>
                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_10"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={data.type_of_change.includes('Dokumen')}
                                label={t('document')}
                                value={'Dokumen'}
                                onChange={(e) => {
                                    if (e.target.checked) {
                                        setData('type_of_change', [...data.type_of_change, e.target.value]);
                                    } else {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== e.target.value));
                                    }
                                }}
                            />
                        </div>
                        <div className="form-check style-check d-flex align-items-center">
                            <CheckBoxInput
                                id="type_of_change_11"
                                errorMessage={errors.type_of_change}
                                errorInput={false}
                                className="checked-danger border border-neutral-300"
                                checked={otherChecked}
                                label={t('other')}
                                value={'other'}
                                onChange={(e) => {
                                    const checked = e.target.checked;
                                    console.log(checked);
                                    setOtherChecked(checked);
                                    if (!checked) {
                                        setData('type_of_change', data.type_of_change.filter(v => v !== otherValue));
                                        setOtherValue('');
                                    }
                                }}
                            />
                        </div>
                    </div>
                </div>
                {otherChecked && (
                    <TextInput
                        className="border p-2 rounded w-full mt-20 mb-20"
                        placeholder={t('please_specify')}
                        value={otherValue}
                        onChange={(e) => {
                            const value = e.target.value;
                            setOtherValue(value);
                            setData(
                                'type_of_change',
                                [...data.type_of_change.filter(v => v !== otherValue), value]
                            );
                        }}
                    />
                )}
            </div >
        </>
    );
}
