

import { useTranslation } from "react-i18next";
import AppLayout from "../Layouts/AppLayout";
import Breadcrumb from "../src/components/ui/Breadcrumb";
import DashBoardLayerOne from "../src/components/DashBoardLayerOne";
export default function Index({ title, description }) {
    const { t } = useTranslation()
    return (
        <>
            <AppLayout>

                <Breadcrumb title={title} />
                {/* DashBoardLayerOne */}
                <DashBoardLayerOne />

            </AppLayout>
        </>
    )
}