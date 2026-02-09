export default function DetailItem({ label, value }) {
    return (
        <li className="detail-item">
            <span className="detail-label">{label}</span>
            <span className="detail-colon">:</span>
            <span className="detail-value">{value ?? '-'}</span>
        </li>
    );
}
