import type { Metadata } from "next";
import { Forum, Nunito_Sans } from "next/font/google";
import "./globals.css";
import { Toaster } from "@/components/ui/toaster";

const forum = Forum({
  variable: "--font-forum",
  subsets: ["latin", "cyrillic"],
  weight: "400",
});

const nunitoSans = Nunito_Sans({
  variable: "--font-nunito",
  subsets: ["latin", "cyrillic"],
  weight: ["300", "400", "600", "700", "800"],
});

export const metadata: Metadata = {
  title: "Тетяна Краснобаєва — Фотограф | Фото та Відео",
  description:
    "Весільний та сімейний фотограф і відеограф у Томаківці та Києві. Індивідуальні, сімейні, весільні, Love Story, днем народження та вагітності зйомки.",
  keywords: [
    "фотограф",
    "Томаківка",
    "Київ",
    "весільна зйомка",
    "сімейна зйомка",
    "Love Story",
    "фотосесія вагітності",
  ],
  authors: [{ name: "Тетяна Краснобаєва" }],
  icons: {
    icon: "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📷</text></svg>",
  },
  openGraph: {
    title: "Фотограф | Томаківка | Київ — Тетяна Краснобаєва",
    description:
      "Весільний та сімейний фотограф і відеограф. Індивідуальні, сімейні та весільні зйомки.",
    type: "website",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="uk" suppressHydrationWarning className="dark">
      <body
        className={`${forum.variable} ${nunitoSans.variable} antialiased bg-background text-foreground`}
      >
        {children}
        <Toaster />
      </body>
    </html>
  );
}
