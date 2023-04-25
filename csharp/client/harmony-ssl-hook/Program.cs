using HarmonyLib;
using System;
using System.IO;
using System.Linq;
using System.Reflection;

namespace harmony_ssl_hook
{
    internal class Program
    {
        private static string AssemblyLoad = "";

        private static string sslKey = "3082010A0282010100ABDA0F3E94C51EDC5DC15E65D0DD98B6AC90EA1F712D1318A081700F5C06B50638456378F97D828D8A7CDFF6907D9A064E1182B62B16B3F4F8D125F8BA1279B42C18D7B14A3356E0F3E0907BBD1B287E33292260E5EBB8B050293AB11E63FEDEFDAFAA6A5DD15EF125832A20A5760BC76B6D10FD3DAAEFDC70924353D699A5C2DD8EF78D1AA5A9F9EFA7EDE7B8DBD893579B2A8EA87FCFF2F50D7E43F75EF8C9D0B01C5D1FB0E9C8E30FFA83AD5BE4A46BD7C707B2B027E5CAA96EF6386617186EFB22ACD2F1231228E75465546DE24C4D54032C3C44594CEC39302FCAD12AE784ACC73FD9E2D43A452A01ABF9ACCE8E124601DD11AFBF43089F636FDB730D270203010001";

        private static Random random = new Random();

        public static string RandomString(int length)
        {
            const string chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            return new string(Enumerable.Repeat(chars, length)
                .Select(s => s[random.Next(s.Length)]).ToArray());
        }
        static void Main(string[] args)
        {
            // check if a valid file was dragged into application
            try
            {
                AssemblyLoad = args[0];
            }
            catch
            {
                while (!File.Exists(AssemblyLoad) || new FileInfo(AssemblyLoad).Extension != ".exe")
                {
                    Console.Clear();
                    Console.WriteLine("Please provide a valid executable File [.EXE]: ");
                    AssemblyLoad = Console.ReadLine();
                }
                Console.Clear();
            }

            // load the file and patch it
            try
            {
                object[] parameters = null;
                var assembly = Assembly.LoadFile(Path.GetFullPath(AssemblyLoad));
                var paraminfo = assembly.EntryPoint.GetParameters();
                parameters = new object[paraminfo.Length];
                Harmony patch = new Harmony(RandomString(15));
                patch.PatchAll(Assembly.GetExecutingAssembly());
                assembly.EntryPoint.Invoke(null, parameters);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Could not load {AssemblyLoad}\n{ex}");
            }

            Console.ReadLine();
        }

        [HarmonyPatch(typeof(System.Security.Cryptography.X509Certificates.X509Certificate), nameof(System.Security.Cryptography.X509Certificates.X509Certificate.GetPublicKeyString))]
        class X509Certificate
        {
            [STAThread]
            static bool Prefix(ref string __result)
            {
                Console.WriteLine("SSL key changed!");
                __result = sslKey;
                return false;
            }
        }
    }
}
