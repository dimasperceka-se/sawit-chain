import { useEffect, useState, type FormEvent } from "react";
import { useLocation } from "wouter";
import { Eye, EyeOff, Check } from "lucide-react";
import coverImg from "@/cover.png";

/* ------------------------------------------------------------------ */
/* Inline brand SVGs                                                   */
/* ------------------------------------------------------------------ */

function PalmTwinMark({ className = "" }: { className?: string }) {
  return (
    <svg viewBox="0 0 40 40" className={className} aria-hidden="true">
      <defs>
        <linearGradient id="pt-cloud" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stopColor="#a99ef0" />
          <stop offset="100%" stopColor="#7c6ce6" />
        </linearGradient>
      </defs>
      <g fill="url(#pt-cloud)">
        <circle cx="16" cy="26" r="6.5" />
        <circle cx="23.5" cy="22" r="8.5" />
        <circle cx="29" cy="27" r="5.5" />
        <rect x="13" y="26" width="19" height="7.5" rx="3.75" />
      </g>
      <circle cx="31" cy="13" r="2.3" fill="#cfc6f5" />
    </svg>
  );
}

function GoogleG({ className = "" }: { className?: string }) {
  return (
    <svg viewBox="0 0 18 18" className={className} aria-hidden="true">
      <path
        fill="#4285F4"
        d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.49h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.92c1.71-1.57 2.68-3.89 2.68-6.63z"
      />
      <path
        fill="#34A853"
        d="M9 18c2.43 0 4.47-.8 5.96-2.17l-2.92-2.26c-.81.54-1.84.86-3.04.86-2.34 0-4.32-1.58-5.03-3.7H.96v2.33A9 9 0 0 0 9 18z"
      />
      <path
        fill="#FBBC05"
        d="M3.97 10.73a5.41 5.41 0 0 1 0-3.46V4.94H.96a9 9 0 0 0 0 8.12l3.01-2.33z"
      />
      <path
        fill="#EA4335"
        d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.89 11.42 0 9 0A9 9 0 0 0 .96 4.94l3.01 2.33C4.68 5.16 6.66 3.58 9 3.58z"
      />
    </svg>
  );
}

function AppleLogo({ className = "" }: { className?: string }) {
  return (
    <svg viewBox="0 0 24 24" fill="currentColor" className={className} aria-hidden="true">
      <path d="M16.36 1.43c0 1.06-.4 2.06-1.18 2.86-.93.96-2.05 1.51-3.27 1.41-.02-1.07.42-2.1 1.16-2.86.81-.84 2.07-1.45 3.13-1.5l.16.09zM20.5 17.2c-.34.79-.5 1.14-.94 1.84-.61.98-1.47 2.2-2.54 2.21-.95.01-1.2-.62-2.49-.61-1.29.01-1.56.62-2.51.61-1.07-.01-1.88-1.11-2.49-2.09-1.71-2.74-1.89-5.95-.83-7.66.75-1.21 1.93-1.92 3.04-1.92 1.13 0 1.84.62 2.78.62.91 0 1.46-.62 2.77-.62.99 0 2.04.54 2.79 1.47-2.45 1.34-2.05 4.84.42 5.95z" />
    </svg>
  );
}

/** Cover artwork for the left panel. */
function CoverArt() {
  return (
    <div className="absolute inset-0">
      <img
        src={coverImg}
        alt=""
        className="absolute inset-0 h-full w-full object-cover"
      />
      {/* darken top + bottom slightly so the logo and tagline stay legible */}
      <div className="absolute inset-0 bg-gradient-to-b from-black/35 via-transparent to-black/75" />
    </div>
  );
}

/* ------------------------------------------------------------------ */
/* Login page                                                          */
/* ------------------------------------------------------------------ */

// Auth is validated server-side (POST /api/login) so no credentials live in the
// client bundle. The expected email/password are set via env on the server
// (AUTH_EMAIL / AUTH_PASSWORD — see .env.example).

export default function Login() {
  const [, navigate] = useLocation();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [remember, setRemember] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const prev = document.title;
    document.title = "Log in · Palm-Twin";
    return () => {
      document.title = prev;
    };
  }, []);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    try {
      const res = await fetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: email.trim(), password }),
      });
      if (res.ok) {
        setError("");
        navigate("/");
      } else {
        setError("Incorrect email or password.");
      }
    } catch {
      setError("Login failed. Please try again.");
    }
  }

  const inputBase =
    "h-12 w-full rounded-lg border border-transparent bg-[#3a3750] px-4 text-[14px] " +
    "text-white placeholder:text-[#87859a] outline-none transition-colors " +
    "focus:border-[#7c6ce6] focus:bg-[#403c5a]";

  const socialBtn =
    "flex h-12 flex-1 items-center justify-center gap-2.5 rounded-lg border border-[#46435c] " +
    "bg-[#322f45] text-[14px] font-medium text-white transition-colors hover:bg-[#3c3953]";

  return (
    <div className="flex min-h-screen w-full items-center justify-center bg-[#16141f] p-4 text-white">
      <div className="flex w-full max-w-[900px] flex-col overflow-hidden rounded-[24px] bg-[#2b2839] p-2.5 md:h-[580px] md:flex-row">
        {/* ---------------- Left: image panel ---------------- */}
        <div className="relative hidden flex-col justify-between overflow-hidden rounded-[18px] p-6 md:flex md:w-[44%]">
          <CoverArt />

          {/* top row */}
          <div className="relative z-10 flex items-center gap-2">
            <PalmTwinMark className="h-7 w-7" />
            <span className="text-[17px] font-semibold tracking-tight text-white">
              Palm-Twin
            </span>
          </div>

          {/* bottom: tagline + carousel dots */}
          <div className="relative z-10">
            <h2 className="text-[26px] font-semibold leading-snug text-white">
              Mapping Plantations,
              <br />
              Protecting Forests
            </h2>
            <div className="mt-5 flex items-center gap-2">
              <span className="h-1.5 w-5 rounded-full bg-white/40" />
              <span className="h-1.5 w-5 rounded-full bg-white/40" />
              <span className="h-1.5 w-8 rounded-full bg-white" />
            </div>
          </div>
        </div>

        {/* ---------------- Right: login form ---------------- */}
        <div className="flex flex-1 flex-col justify-center px-6 py-8 sm:px-10 md:py-6">
          <div className="mx-auto w-full max-w-[380px]">
            <h1 className="text-[32px] font-bold leading-tight tracking-tight text-white">
              Welcome back
            </h1>
            <p className="mt-2 text-[14px] text-[#9c9aac]">
              Log in to your Palm-Twin account
            </p>

            <form onSubmit={handleSubmit} className="mt-7 space-y-4">
              <input
                type="email"
                placeholder="Email"
                aria-label="Email"
                autoComplete="email"
                value={email}
                onChange={(e) => {
                  setEmail(e.target.value);
                  if (error) setError("");
                }}
                className={inputBase}
              />

              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  placeholder="Enter your password"
                  aria-label="Password"
                  autoComplete="current-password"
                  value={password}
                  onChange={(e) => {
                    setPassword(e.target.value);
                    if (error) setError("");
                  }}
                  className={inputBase + " pr-11"}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword((s) => !s)}
                  aria-label={showPassword ? "Hide password" : "Show password"}
                  className="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9c9aac] hover:text-white"
                >
                  {showPassword ? (
                    <Eye className="h-[18px] w-[18px]" />
                  ) : (
                    <EyeOff className="h-[18px] w-[18px]" />
                  )}
                </button>
              </div>

              <div className="flex items-center justify-between pt-0.5">
                <label className="flex cursor-pointer select-none items-center gap-2.5">
                  <button
                    type="button"
                    role="checkbox"
                    aria-checked={remember}
                    onClick={() => setRemember((r) => !r)}
                    className={
                      "flex h-[18px] w-[18px] items-center justify-center rounded-[5px] border transition-colors " +
                      (remember
                        ? "border-[#7c6ce6] bg-[#7c6ce6]"
                        : "border-[#5a576f] bg-transparent")
                    }
                  >
                    {remember && <Check className="h-3 w-3 text-white" strokeWidth={3} />}
                  </button>
                  <span className="text-[13px] text-[#c4c2d2]">Remember me</span>
                </label>
                <button
                  type="button"
                  className="text-[13px] font-medium text-[#a99ef0] hover:text-white"
                >
                  Forgot password?
                </button>
              </div>

              {error && (
                <p role="alert" className="text-[13px] text-[#ff6b6b]">
                  {error}
                </p>
              )}

              <button
                type="submit"
                className="mt-1 h-12 w-full rounded-lg bg-[#7c6ce6] text-[15px] font-semibold text-white transition-colors hover:bg-[#8b7cf0] active:bg-[#6f5fda]"
              >
                Log in
              </button>
            </form>

            {/* divider */}
            <div className="my-5 flex items-center gap-3">
              <span className="h-px flex-1 bg-[#403d54]" />
              <span className="text-[13px] text-[#87859a]">Or log in with</span>
              <span className="h-px flex-1 bg-[#403d54]" />
            </div>

            {/* social */}
            <div className="flex gap-3.5">
              <button type="button" className={socialBtn}>
                <GoogleG className="h-[18px] w-[18px]" />
                Google
              </button>
              <button type="button" className={socialBtn}>
                <AppleLogo className="h-[18px] w-[18px]" />
                Apple
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
