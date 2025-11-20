using Microsoft.AspNetCore.Mvc;
using System.Data;
using VistaDB.Provider;

namespace VistaDbApi.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class DataController : ControllerBase
    {
        private const string ConnectionString = "Data Source=Data/mydb.vdb4";

        [HttpGet]
        public IActionResult Get()
        {
            var results = new List<Dictionary<string, object>>();

            using (var conn = new VistaDBConnection(ConnectionString))
            {
                conn.Open();
                using (var cmd = conn.CreateCommand())
                {
                    cmd.CommandText = "SELECT * FROM YourTable"; // replace with your table
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            var row = new Dictionary<string, object>();
                            for (int i = 0; i < reader.FieldCount; i++)
                            {
                                row[reader.GetName(i)] = reader.GetValue(i);
                            }
                            results.Add(row);
                        }
                    }
                }
            }

            return Ok(results);
        }
    }
}
