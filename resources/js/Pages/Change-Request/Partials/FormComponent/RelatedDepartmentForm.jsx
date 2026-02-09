import { useTranslation } from "react-i18next";
import TextInput from "../../../../src/components/ui/TextInput";
import TextAreaInput from "../../../../src/components/ui/TextAreaInput";
import Select from "react-select";
import { useEffect } from "react";

export default function RelatedDepartmentForm({ impactOfChangeCategories, data, setData, errors, clearErrors, reset }) {
    const { t } = useTranslation();

    // kondisi: kalau ada select, maka text disable, dan sebaliknya
    const isSelectFilled = !!data?.impact_of_change_category;
    const isCustomFilled = !!data?.custom_category;



    return (
        <div className="row">
            {/* Select */}
            <div className="col-6">
                <div className="mb-3">
                    <label className="form-label fw-bold">
                        {t("impact_of_change_categories")}
                    </label>
                    <Select
                        options={impactOfChangeCategories}
                        getOptionLabel={(item) => item.name}
                        getOptionValue={(item) => item.name}
                        value={
                            impactOfChangeCategories.find(
                                (opt) => opt.name === data.impact_of_change_category
                            ) || null
                        }
                        onChange={(val) => {
                            clearErrors('impact_of_change_category');
                            setData("impact_of_change_category", val?.name);
                            if (val) setData("custom_category", "");
                        }}
                        placeholder={t("select_category")}
                        isClearable
                        isSearchable
                        menuPortalTarget={document.body}
                        styles={{
                            menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                            menu: (base) => ({ ...base, zIndex: 9999 }),
                        }}
                        isDisabled={isCustomFilled} // disable jika text diisi
                    />
                    {errors?.impact_of_change_category && (
                        <div className="text-danger mt-2">
                            {errors?.impact_of_change_category}
                        </div>
                    )}
                </div>
            </div>

            {/* TextInput */}
            <div className="col-6">
                <div className="mb-3">
                    <label className="form-label fw-bold">
                        {t("impact_of_change_categories")} ({t("other")})
                    </label>
                    <TextInput
                        value={data.custom_category || ""}
                        onChange={(e) => {
                            const value = e.target.value;
                            setData("custom_category", value);
                            if (value) setData("impact_of_change_category", null); // reset select
                        }}
                        placeholder={t("enter_attribute", {
                            attribute: t("category"),
                        })}
                        errorMessage={errors?.custom_category}
                        disabled={isSelectFilled} // disable jika select diisi
                    />
                </div>
                {errors?.custom_category && (
                    <div className="text-danger mt-2">
                        {errors?.custom_category}
                    </div>
                )}
            </div>
            <div className="col-12">
                <div className="mb-3">
                    <label className="form-label fw-bold">
                        {t("impact_of_change_description")}
                    </label>
                    <TextAreaInput
                        autoComplete="off"
                        placeholder={t('enter_attribute', { 'attribute': t('impact_of_change_description') })}
                        value={data.impact_of_change_description}
                        onChange={(e) => setData('impact_of_change_description', e.target.value)}
                        errorMessage={errors.impact_of_change_description}
                    />
                </div>
            </div>
            <div className="col-12">
                <div className="mb-3">
                    <label className="form-label fw-bold">
                        {t("due_date")}
                    </label>
                    <TextInput
                        type="date"
                        value={data.deadline || ""}
                        onChange={(e) => {
                            const value = e.target.value;
                            setData("deadline", value);
                        }}
                        placeholder={t("enter_attribute", {
                            attribute: t("due_date"),
                        })}
                        errorMessage={errors?.deadline}
                    />
                </div>
            </div>

            {/* <div className="col-6">
                <div className="mb-3">
                    <label className="form-label fw-bold">
                        {t("realization")}
                    </label>
                    <TextInput
                        value={data.realization || ""}
                        onChange={(e) => {
                            const value = e.target.value;
                            setData("realization", value);
                        }}
                        placeholder={t("enter_attribute", {
                            attribute: t("realization"),
                        })}
                        errorMessage={errors?.realization}
                    />
                </div>
            </div> */}
        </div>
    );
}
