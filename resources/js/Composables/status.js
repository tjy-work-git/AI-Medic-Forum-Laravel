export function useStatusName( status )
{
    switch( status )
    {
        case 503 : return 'Service Unavailable';
        case 500 : return 'Server Error';
        case 419 : return 'Session Expired';
        case 418 : return 'I\'m a teapot';
        case 404 : return 'Page Not Found';
        case 403 : return 'Forbidden';
        default : return 'Error';
    }
}

export function useStatusMessage( status )
{
    switch( status )
    {
        case 503 : return 'Service Unavailable.';
        case 500 : return 'Server Error.';
        case 419 : return 'Session Expired.';
        case 418 : return 'Haha look! I\'m a teapot!!!';
        case 404 : return 'Not Found.';
        case 403 : return 'Forbidden.';
        default : return 'Something went wrong unexpectedly.';
    }
}