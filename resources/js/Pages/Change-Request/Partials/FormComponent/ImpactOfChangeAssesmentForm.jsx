import { useTranslation } from "react-i18next";
import TextInput from "../../../../src/components/ui/TextInput";
import CheckBoxInput from "../../../../src/components/ui/CheckBoxInput";
import ErrorInput from "../../../../src/components/ui/ErrorInput";
import Select from 'react-select'
import { useState } from "react";
export default function ImpactOfChangeAssesmentForm({ data, setData, errors, setError, products, clearErrors }) {
    const { t } = useTranslation();
    const [selectedOptions, setSelectedOptions] = useState(
        products
            .filter(p => data.affected_products.includes(p.id))
            .map(p => ({ value: p.id, label: p.name }))
    );

    const handleChange = (selected) => {
        setSelectedOptions(selected || []);
        setData("affected_products", (selected || []).map(s => s.value));
        clearErrors("affected_products");
    };

    {console.log(data)}
    return (
        <div className='row gy-3'>
            <div className='col-sm-12'>
                <label className='form-label'>{t('product_question')}</label>
                {errors.product_affected && <small className="text-danger d-block mb-10">{errors.product_affected}</small>}
                <div className="form-check style-check d-flex align-items-center">
                    <CheckBoxInput
                        type="radio"
                        id="product_yes"
                        label={t('product_yes')}
                        errorMessage={errors.product_affected}
                        errorInput={false}
                        className="checked-danger border border-neutral-300"
                        onChange={(e) => {
                            setData('product_affected', e.target.value)
                        }}
                        checked={data.product_affected == 'Yes'}
                        value={"Yes"}
                    />
                </div>
                <div className="form-check style-check d-flex align-items-center mb-20">
                    <CheckBoxInput
                        type="radio"
                        id="product_no"
                        label={t('product_no')}
                        errorMessage={errors.product_affected}
                        errorInput={false}
                        className="checked-danger border border-neutral-300"
                        onChange={(e) => {
                            setData('product_affected', e.target.value)
                        }}
                        checked={data.product_affected == 'No'}
                        value={"No"}
                    />
                </div>
                {data.product_affected == 'Yes' && (
                    <>
                        <label className='form-label'>{t('select_affected_products')}</label>
                        <Select
                            isMulti
                            options={products.map(p => ({ value: p.id, label: p.name }))}
                            value={selectedOptions}
                            onChange={handleChange}
                            placeholder={t("search_products")}
                        />
                      
                        {/* Daftar pilihan tampil sebagai checkbox */}
                        <div className="mt-3">
                            <label htmlFor="">{t('selected_products')}</label>
                            {selectedOptions.map((option) => (
                                <div key={option.value} className="form-check style-check d-flex align-items-center">
                                    <input
                                        type="checkbox"
                                        className="form-check-input me-2"
                                        checked={data.affected_products.includes(option.value)}
                                        onChange={() => {
                                            const updated = data.affected_products.filter(id => id !== option.value);
                                            setData("affected_products", updated);
                                            setSelectedOptions(selectedOptions.filter(o => o.value !== option.value));
                                        }}
                                    />
                                    <label className="form-check-label">{option.label}</label>
                                </div>
                            ))}
                        </div>
                        {errors.affected_products && (
                            <ErrorInput message={errors.affected_products} className="mt-3 text-danger" />
                        )}
                    </>
                )}
            </div>
            <hr />

            <div className='col-sm-12'>
                <label className='form-label text'>{t('third_party_question')}</label>
                {errors.third_party_involved && <small className="text-danger d-block mb-10">{errors.third_party_involved}</small>}
                <div className="form-check style-check d-flex align-items-center">
                    <CheckBoxInput
                        type="radio"
                        id="third_party_yes"
                        label={t('yes')}
                        errorMessage={errors.third_party_involved}
                        errorInput={false}
                        className="checked-danger border border-neutral-300"
                        onChange={(e) => {
                            setData('third_party_involved', e.target.value === 'true')
                        }}
                        checked={data.third_party_involved}
                        value={true}
                    />
                </div>
                <div className="form-check style-check d-flex align-items-center">
                    <CheckBoxInput
                        type="radio"
                        id="third_party_no"
                        label={t('no')}
                        errorMessage={errors.third_party_involved}
                        errorInput={false}
                        className="checked-danger border border-neutral-300"
                        onChange={(e) => {
                            setData('third_party_involved', e.target.value === 'true')
                        }}
                        checked={data.third_party_involved == false}
                        value={false}
                    />
                </div>

                {data.third_party_involved && (
                    <TextInput
                        className="form-control mt-3"
                        placeholder={t('please_specify_third_party_name')}
                        value={data.third_party_name}
                        onChange={(e) => setData('third_party_name', e.target.value)}
                        errorMessage={errors.third_party_name}
                    />
                )}
            </div>
        </div>
    )
}
