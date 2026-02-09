export default function ErrorInput({ message, className = '', ...props }) {
    return message ? (
        <div 
            {...props}
            className={`text-danger  ${className}`}
        >
            {message}
        </div>
    ) : null;
}
