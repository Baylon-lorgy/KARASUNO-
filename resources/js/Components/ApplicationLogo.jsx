import React from 'react';

export default function ApplicationLogo(props) {
    return (
        <div
            style={{
                background: 'linear-gradient(135deg, #537895 0%, #09203f 100%)',
                borderRadius: '50%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                boxShadow: '0 2px 12px 0 rgba(34, 56, 139, 0.10)',
                border: '3px solid #537895',
                width: 90,
                height: 90,
                overflow: 'hidden',
            }}
        >
            <img
                src="https://scontent.fcgy3-1.fna.fbcdn.net/v/t39.30808-6/497474398_2187229995127815_4641273098542298967_n.jpg?_nc_cat=104&ccb=1-7&_nc_sid=bd9a62&_nc_eui2=AeEsk9RrjQGiDm7RTUh2jldYJ0Qt9AiMp8onRC30CIynyqaeTwoXZd5yJ-B_1AfAw4194Kx9h0pn3meEGWLjHuOV&_nc_ohc=dp9D66peByoQ7kNvwHd7zMG&_nc_oc=AdmXAvjL4q5b83DozmsHJLU3LOzdQS_A4OmUjz1-s1oxUp5NkLF-nOajikSHC0kmLy8&_nc_zt=23&_nc_ht=scontent.fcgy3-1.fna&_nc_gid=auxWtnhAVFrO57N1IcaeaA&oh=00_AfLWIAUOyw1xc3pDgr2aWGMkUKjNA1d_Ax5oDCLkWsatSw&oe=682BC366"
                alt="Logo"
                style={{ width: '100%', height: '100%', borderRadius: '50%', objectFit: 'cover', display: 'block' }}
                {...props}
            />
        </div>
    );
}
