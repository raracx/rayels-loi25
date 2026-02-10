export interface Loi25Config {
  lang?: 'fr' | 'en';
  position?: 'top' | 'bottom';
  theme?: 'light' | 'dark';
  privacyPolicyUrl?: string;
  poweredByLink?: boolean;
  expiryDays?: number;
  onAcceptAll?: () => void;
  onAcceptNecessary?: () => void;
  onRevoke?: () => void;
  texts?: Loi25Texts;
}

export interface Loi25Texts {
  title: string;
  message: string;
  acceptAll: string;
  acceptNecessary: string;
  manage: string;
  privacyPolicy: string;
  poweredBy: string;
}

export function init(config?: Loi25Config): void;
export function getConsent(): 'all' | 'necessary' | null;
export function setConsent(level: 'all' | 'necessary'): void;
export function revokeConsent(): void;
export function isAnalyticsAllowed(): boolean;
export const TEXTS: { fr: Loi25Texts; en: Loi25Texts };
